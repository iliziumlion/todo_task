<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<style>
    [v-cloak] {
        display: none;
    }
</style>

<script src="https://unpkg.com/vue@3"></script>

<div class="container mt-4" id="app" v-cloak>
    <h2>Todo Manager</h2>

    <!-- Search -->
    <div class="form-inline mb-3 row g-3">
        <div class="col-auto">
            <input
                    type="text"
                    class="form-control me-2"
                    placeholder="Search for tasks"
                    v-model="searchQuery"
            >
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" @click="loadTasks(1)">
                Find
            </button>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" @click="clearTasks">
                Clean
            </button>
        </div>
    </div>

    <!-- Button for showing a form of adding a task -->
    <button class="btn btn-success mb-3" @click="toggleAddForm">
        {{ showAddForm ? 'Hide the form' : 'Add the task' }}
    </button>

    <!-- Form of adding a new task -->
    <div v-if="showAddForm" class="border p-3 mb-4">
        <h5>A new task</h5>
        <div class="form-group mb-2">
            <label>Name</label>
            <input type="text" class="form-control" v-model="newTask.NAME">
        </div>
        <div class="form-group mb-2">
            <label>Description</label>
            <textarea class="form-control" v-model="newTask.DESCRIPTION"></textarea>
        </div>
        <div class="form-group mb-2">
            <label>Tags (select)</label>
            <select class="form-control" multiple v-model="newTask.TAGS">
                <option
                        v-for="tag in allTags"
                        :key="tag.ID"
                        :value="tag.ID"
                >
                    {{ tag.NAME }}
                </option>
            </select>
        </div>
        <button class="btn btn-primary" @click="addTask">
            Add
        </button>
    </div>

    <!-- Task table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Tags</th>
            <th style="width:180px;">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="task in tasks" :key="task.ID">
            <td>{{ task.ID }}</td>
            <td>
                <div v-if="editTaskId === task.ID">
                    <input
                            type="text"
                            class="form-control"
                            v-model="editTask.NAME"
                    >
                </div>
                <div v-else>
                    {{ task.NAME }}
                </div>
            </td>
            <td>
                <div v-if="editTaskId === task.ID">
                        <textarea
                                class="form-control"
                                v-model="editTask.DESCRIPTION"
                        ></textarea>
                </div>
                <div v-else>
                    {{ task.DESCRIPTION }}
                </div>
            </td>
            <td>
                <div v-if="editTaskId === task.ID">
                    <select
                            class="form-control"
                            multiple
                            v-model="editTask.TAGS"
                    >
                        <option
                                v-for="tag in allTags"
                                :key="tag.ID"
                                :value="tag.ID"
                        >
                            {{ tag.NAME }}
                        </option>
                    </select>
                </div>
                <div v-else>
                    <span class="me-2">
                        {{ findTagName(task.TAGS) }}
                    </span>
                </div>
            </td>
            <td>
                <div v-if="editTaskId === task.ID" class="d-flex  justify-content-end gap-2">
                    <button class="btn btn-sm btn-primary me-2" @click="saveEditTask(task.ID)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viedwBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-save-icon lucide-save">
                            <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                            <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/>
                            <path d="M7 3v4a1 1 0 0 0 1 1h7"/>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-secondary" @click="cancelEditTask">
                        cancel
                    </button>
                </div>
                <div class="d-flex justify-content-end gap-2" v-else>
                    <button class="btn btn-sm btn-warning me-2" @click="startEditTask(task)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-pencil-ruler-icon lucide-pencil-ruler">
                            <path d="M13 7 8.7 2.7a2.41 2.41 0 0 0-3.4 0L2.7 5.3a2.41 2.41 0 0 0 0 3.4L7 13"/>
                            <path d="m8 6 2-2"/>
                            <path d="m18 16 2-2"/>
                            <path d="m17 11 4.3 4.3c.94.94.94 2.46 0 3.4l-2.6 2.6c-.94.94-2.46.94-3.4 0L11 17"/>
                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                            <path d="m15 5 4 4"/>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-danger" @click="deleteTask(task.ID)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-trash2-icon lucide-trash-2">
                            <path d="M3 6h18"/>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                            <line x1="10" x2="10" y1="11" y2="17"/>
                            <line x1="14" x2="14" y1="11" y2="17"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav v-if="totalPages > 1">
        <ul class="pagination">
            <li
                    class="page-item"
                    :class="{ active: n === currentPage }"
                    v-for="n in totalPages"
                    :key="n"
            >
                <a href="#" class="page-link" @click.prevent="loadTasks(n)">
                    {{ n }}
                </a>
            </li>
        </ul>
    </nav>
</div>

<script>
    Vue.createApp({
        data() {
            return {
                tasks: [],
                allTags: [],
                searchQuery: '',
                totalPages: 1,
                currentPage: 1,
                showAddForm: false,
                newTask: {
                    NAME: '',
                    DESCRIPTION: '',
                    TAGS: []
                },
                editTaskId: null,
                editTask: {
                    ID: null,
                    NAME: '',
                    DESCRIPTION: '',
                    TAGS: []
                }
            }
        },
        mounted() {
            this.loadTags();
            this.loadTasks(1);
        },
        methods: {
            clearTasks() {
                this.searchQuery = '';
            },
            /**
             * Switching of the show "New task"
             */
            toggleAddForm() {
                this.showAddForm = !this.showAddForm;
            },

            /**
             * Download tags ( AJAX: ?ajax=Y&action=loadTags )
             */
            async loadTags() {
                try {
                    const resp = await fetch('?ajax=Y&action=loadTags');
                    const data = await resp.json();
                    if (data.success) {
                        this.allTags = data.data;
                    }
                } catch (e) {
                    console.error('Ошибка при загрузке тегов:', e);
                }
            },

            /**
             * Download tasks ( AJAX: ?ajax=Y&action=loadTasks )
             */
            async loadTasks(page = 1) {
                try {
                    const params = new URLSearchParams();
                    params.set('ajax', 'Y');
                    params.set('action', 'loadTasks');
                    params.set('page', page);
                    if (this.searchQuery) {
                        params.set('search', this.searchQuery);
                    }
                    const resp = await fetch('?' + params.toString());
                    const data = await resp.json();
                    if (data.success) {
                        this.tasks = data.data.ITEMS;
                        this.totalPages = data.data.TOTAL_PAGES;
                        this.currentPage = data.data.CURRENT_PAGE;
                    }
                } catch (e) {
                    console.error('Ошибка при загрузке задач:', e);
                }
            },

            /**
             *
             Add a new task
             */
            async addTask() {
                try {
                    const postData = {
                        NAME: this.newTask.NAME,
                        DESCRIPTION: this.newTask.DESCRIPTION,
                        TAGS: this.newTask.TAGS
                    };
                    const resp = await fetch('?ajax=Y&action=addTask', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(postData)
                    });
                    const data = await resp.json();

                    if (data.success) {
                        this.newTask.NAME = '';
                        this.newTask.DESCRIPTION = '';
                        this.newTask.TAGS = [];
                        this.showAddForm = false;
                        this.loadTasks(this.currentPage);
                    } else {
                        alert(data.message || 'Ошибка при создании задачи');
                    }
                } catch (e) {
                    console.error('Ошибка при добавлении задачи:', e);
                    alert('Произошла ошибка при добавлении задачи');
                }
            },

            /**
             * Start editing the task
             */
            startEditTask(task) {
                this.editTaskId = task.ID;
                this.editTask.ID = task.ID;
                this.editTask.NAME = task.NAME;
                this.editTask.DESCRIPTION = task.DESCRIPTION;
                this.editTask.TAGS = [...task.TAGS];
            },

            /**
             * Save changes in editing
             */
            async saveEditTask() {
                try {
                    const postData = {
                        ID: this.editTask.ID,
                        NAME: this.editTask.NAME,
                        DESCRIPTION: this.editTask.DESCRIPTION,
                        TAGS: this.editTask.TAGS
                    };
                    const resp = await fetch('?ajax=Y&action=editTask', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(postData)
                    });
                    const data = await resp.json();

                    if (data.success) {
                        //alert('Задача обновлена');
                        this.editTaskId = null;
                        this.loadTasks(this.currentPage);
                    } else {
                        alert(data.message || 'Ошибка при редактировании');
                    }
                } catch (e) {
                    console.error('Ошибка при сохранении задачи:', e);
                    alert('Произошла ошибка при сохранении задачи');
                }
            },

            /**
             * The abolition of editing
             */
            cancelEditTask() {
                this.editTaskId = null;
            },

            /**
             * Removing the task
             */
            async deleteTask(taskId) {
                const confirmDelete = confirm('Удалить задачу?');
                if (!confirmDelete) return;

                try {
                    const resp = await fetch(`?ajax=Y&action=deleteTask&id=${taskId}`);
                    const data = await resp.json();
                    if (data.success) {
                        //alert('Задача удалена');
                        this.loadTasks(this.currentPage);
                    } else {
                        alert(data.message || 'Ошибка при удалении');
                    }
                } catch (e) {
                    console.error('Ошибка при удалении задачи:', e);
                    alert('Произошла ошибка при удалении задачи');
                }
            },

            /**
             *
             Find the name Tag by ID
             */
            findTagName(tagId) {
                const tag = this.allTags.find(t => t.ID == tagId);
                return tag ? tag.NAME : '';
            }
        },
        watch: {
            searchQuery(newVal) {
                if (newVal.trim() === '' || newVal.trim().length > 1) {
                    this.loadTasks(1);
                }
            }
        },
    }).mount('#app');
</script>