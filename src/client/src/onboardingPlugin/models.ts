export interface Task {
  id: number;
  notes?: string;
  dueDate?: string;
  isCompleted: boolean;
}

export interface TaskGroup {
  id: number;
  notes?: string;
  taskGroups: Task[];
}
