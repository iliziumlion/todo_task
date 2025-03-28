<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Iblock\Elements\ElementTasksTable;
use Bitrix\Main\Application;

class TodoManagerComponent extends CBitrixComponent
{
    protected function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new \Exception('Модуль iblock не установлен');
        }
    }

    public function onPrepareComponentParams($params)
    {
        $params['IBLOCK_ID_TASKS'] = (int)($params['IBLOCK_ID_TASKS'] ?? 0);
        $params['IBLOCK_ID_TAGS']  = (int)($params['IBLOCK_ID_TAGS'] ?? 0);
        $params['PAGE_SIZE']       = (int)($params['PAGE_SIZE'] ?? 5);
        return $params;
    }

    /**
     * Loading available tags from infoblock Tags.
     */
    protected function loadAllTags(): array
    {
        $iblockId = $this->arParams['IBLOCK_ID_TAGS'];
        if (!$iblockId) {
            return [];
        }

        $allTags = [];
        $res = CIBlockElement::GetList(
            ["NAME" => "ASC"],
            ["IBLOCK_ID" => $iblockId, "ACTIVE" => "Y"],
            false,
            false,
            ["ID", "NAME"]
        );
        while ($tag = $res->Fetch()) {
            $allTags[] = [
                'ID'   => $tag['ID'],
                'NAME' => $tag['NAME']
            ];
        }

        return $allTags;
    }

    /**
     * Loading tasks taking into account the search and pagination.
     */
    protected function loadTasks(): array
    {
        $request   = Context::getCurrent()->getRequest();
        $pageNum   = max(1, (int) $request->get('page'));
        $search    = trim($request->get('search'));
        $pageSize  = $this->arParams['PAGE_SIZE'];
        $iblockId  = $this->arParams['IBLOCK_ID_TASKS'];
        
        $filter = [
            'IBLOCK_ID' => $iblockId,
            'ACTIVE'    => 'Y',
        ];

        if ($search) {
            $filter[] = [
                'LOGIC' => 'OR',
                ['NAME'         => "%$search%"],
                ['DETAIL_TEXT'  => "%$search%"],
                ['PROPERTY_TAGS'=> "%$search%"]
            ];
        }

        $countRes   = CIBlockElement::GetList([], $filter, [], false, ["ID"]);
        $totalCount = $countRes;

        $res = CIBlockElement::GetList(
            ["ID" => "DESC"],
            $filter,
            false,
            ["iNumPage" => $pageNum, "nPageSize" => $pageSize],
            ["ID", "NAME", "DETAIL_TEXT", "PROPERTY_TAGS"]
        );

        $tasks = [];
        while ($item = $res->GetNext()) {
            $tasks[] = [
                'ID'          => $item['ID'],
                'NAME'        => $item['NAME'],
                'DESCRIPTION' => $item['DETAIL_TEXT'],
                'TAGS'        => $item['PROPERTY_TAGS_VALUE']
            ];
        }

        return [
            'ITEMS'       => $tasks,
            'TOTAL_COUNT' => $totalCount,
            'CURRENT_PAGE'=> $pageNum,
            'TOTAL_PAGES' => ceil($totalCount / $pageSize),
        ];
    }

    /**
     * Add new task
     */
    protected function addTask($data)
    {
        $iblockId = $this->arParams['IBLOCK_ID_TASKS'];
        $name     = trim($data['NAME'] ?? '');
        if (!$name || !$iblockId) {
            return false;
        }

        $description = trim($data['DESCRIPTION'] ?? '');
        $tags        = $data['TAGS'] ?? [];

        $el = new CIBlockElement();
        $fields = [
            "IBLOCK_ID"         => $iblockId,
            "NAME"              => $name,
            "ACTIVE"            => "Y",
            "DETAIL_TEXT"       => $description,
            "DETAIL_TEXT_TYPE"  => "text",
            "PROPERTY_TAGS"     => $tags,
        ];

        if ($taskId = $el->Add($fields)) {
            CIBlockElement::SetPropertyValuesEx($taskId, $iblockId, [
                'TAGS' => $tags
            ]);
            return $taskId;
        }

        return false;
    }

    /**
     * Edit task
     */
    protected function editTask($data)
    {
        $iblockId   = $this->arParams['IBLOCK_ID_TASKS'];
        $taskId     = (int)($data['ID'] ?? 0);
        $name       = trim($data['NAME'] ?? '');
        if (!$taskId || !$name || !$iblockId) {
            return false;
        }

        $description = trim($data['DESCRIPTION'] ?? '');
        $tags        = $data['TAGS'] ?? [];

        $el = new CIBlockElement();
        $fields = [
            "NAME"              => $name,
            "DETAIL_TEXT"       => $description,
            "DETAIL_TEXT_TYPE"  => "text",
        ];
        $res = $el->Update($taskId, $fields);

        if ($res) {
            CIBlockElement::SetPropertyValuesEx($taskId, $iblockId, [
                'TAGS' => $tags
            ]);
            return true;
        }
        return false;
    }

    /**
     * Delete task
     */
    protected function deleteTask($taskId)
    {
        $taskId = (int)$taskId;
        if ($taskId > 0) {
            CIBlockElement::Delete($taskId);
            return true;
        }
        return false;
    }

    /**
     * Ajax processing (for ajax = Y).
     * Return JSON and complete the execution.
     */
    protected function handleAjax()
    {
        $request = Context::getCurrent()->getRequest();
        $action  = $request->get('action') ?: $request->getPost('action');
        $result  = ['success' => false, 'message' => '', 'data' => []];

        switch ($action) {
            case 'loadTasks':
                $tasksData = $this->loadTasks();
                $result['success'] = true;
                $result['data']    = $tasksData;
                break;

            case 'addTask':
                $postData = json_decode($request->getInput(), true);
                $taskId   = $this->addTask($postData);
                if ($taskId) {
                    $result['success'] = true;
                    $result['data']    = ['ID' => $taskId];
                } else {
                    $result['message'] = 'Ошибка при добавлении задачи';
                }
                break;

            case 'editTask':
                $postData = json_decode($request->getInput(), true);
                if ($this->editTask($postData)) {
                    $result['success'] = true;
                } else {
                    $result['message'] = 'Ошибка при редактировании задачи';
                }
                break;

            case 'deleteTask':
                $taskId = (int)$request->get('id');
                if ($this->deleteTask($taskId)) {
                    $result['success'] = true;
                } else {
                    $result['message'] = 'Ошибка при удалении задачи';
                }
                break;

            case 'loadTags':
                $tags = $this->loadAllTags();
                $result['success'] = true;
                $result['data']    = $tags;
                break;

            default:
                $result['message'] = 'Неизвестное действие';
        }

        header('Content-Type: application/json');
        echo json_encode($result);
        Application::getInstance()->end();
    }

    public function executeComponent()
    {
        try {
            $this->checkModules();
        } catch (\Exception $e) {
            ShowError($e->getMessage());
            return;
        }

        $request = Context::getCurrent()->getRequest();
        if ($request->get('ajax') === 'Y') {
            $this->handleAjax();
            return;
        }
        $this->includeComponentTemplate();
    }
}