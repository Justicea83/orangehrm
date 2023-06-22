export interface Task {
  id: number;
  notes?: string;
  dueDate?: string;
  isCompleted: boolean;
}

export interface SimpleUser {
  id: number;
  name: number;
}

export interface TaskGroup {
  id: number;
  notes?: string;
  submittedAt?: string | null;
  taskGroups: Task[];
  assignee: SimpleUser;
}
