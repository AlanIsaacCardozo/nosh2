<template>
  <div class="relative p-2 flex overflow-x-auto h-full pt-5 bg-blue-500">
    <!-- Columns (Statuses) -->
    <div
      v-for="status in statuses"
      :key="status.id"
      class="mr-6 w-4/5 max-w-sm  flex-shrink-0"
    >
      <div class="rounded-md shadow-md overflow-hidden">
        <div class="p-3 flex justify-between items-baseline bg-blue-800 ">
          <h4 class="font-medium text-white">
            {{ status.title }}
          </h4>
          <button
            @click="openAddTaskForm(status.id)"
            class="py-1 px-2 text-sm text-orange-500 hover:underline"
          >
            Add Task
          </button>
        </div>
        <div class="p-2 bg-blue-100">
          <!-- AddTaskForm -->
          <AddTaskForm
            v-if="newTaskForStatus === status.id"
            :status-id="status.id"
            v-on:task-added="handleTaskAdded"
            v-on:task-canceled="closeAddTaskForm"
          />
          <!-- ./AddTaskForm -->

          <!-- Tasks -->
          <draggable
            class="flex-1 overflow-hidden"
            v-model="status.tasks"
            v-bind="taskDragOptions"
            @end="handleTaskMoved"
          >
            <transition-group
              class="flex-1 flex flex-col h-full overflow-x-hidden overflow-y-auto rounded shadow-xs"
              tag="div"
            >
              <div
                v-for="task in status.tasks"
                :key="task.id"
                class="mb-3 p-4 flex flex-row bg-white rounded-md shadow transform hover:shadow-md cursor-pointer"
              >
                <div class="w-4/5">
                  <span class="block mb-2 text-xl text-gray-900">
                    {{ task.title }}
                  </span>
                  <p class="text-gray-700">
                    {{ task.description }}
                  </p>
                </div>
                <div class="w-1/5 text-right text-blue-500">
                  {{task.count}}
                </div>
              </div>
              <!-- ./Tasks -->
            </transition-group>
          </draggable>
          <!-- No Tasks -->
          <div
            v-show="!status.tasks.length && newTaskForStatus !== status.id"
            class="flex-1 p-4 flex flex-col items-center justify-center"
          >
            <span class="text-gray-600">No tasks yet</span>
            <button
              class="mt-1 text-sm text-orange-600 hover:underline"
              @click="openAddTaskForm(status.id)"
            >
              Add one
            </button>
          </div>
          <!-- ./No Tasks -->
        </div>
      </div>
    </div>
    <!-- ./Columns -->
    <!-- <FormDetail/> -->
  </div>
</template>

<script>
import draggable from "vuedraggable";
import AddTaskForm from "./AddTaskForm";
import FormDetail from "./FormDetail";

export default {
  components: { draggable, AddTaskForm, FormDetail },
  props: ['initialData'],
  data() {
    return {
      statuses: this.initialData,
      newTaskForStatus: 0
    };
  },
  computed: {
    taskDragOptions() {
      return {
        animation: 200,
        group: "task-list",
        dragClass: "status-drag"
      };
    }
  },
  mounted() {
    // // 'clone' the statuses so we don't alter the prop when making changes
    
    this.statuses = this.initialData;
  },
  methods: {
    openAddTaskForm(statusId) {
      this.newTaskForStatus = statusId;
    },
    closeAddTaskForm() {
      this.newTaskForStatus = 0;
    },
    handleTaskAdded(newTask) {
      // Find the index of the status where we should add the task
      console.log(newTask);
      debugger;
      const statusIndex = this.statuses.findIndex(
        status => status.id === newTask.status_id
      );

      // Add newly created task to our column
      this.statuses[statusIndex].tasks.push(newTask);

      // Reset and close the AddTaskForm
      this.closeAddTaskForm();
    },
    handleTaskMoved(evt) {
      console.log(this.statuses);
      axios.put("/kanban/sync", { columns: this.statuses }).catch(err => {
        console.log(err.response);
      });
    }
  }
};
</script>

<style scoped>
.status-drag {
  transition: transform 0.5s;
  transition-property: all;
}
</style>
