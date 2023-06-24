export interface Task {
  id: number;
  notes?: string;
  dueDate?: string;
  isCompleted: boolean;
}

export interface SimpleUser {
  id: number;
  name: number | null;
  avatar: string | null;
}

export interface TaskGroup {
  id: number;
  notes?: string;
  submittedAt?: string | null;
  taskGroups: Task[];
  assignee: SimpleUser;
  creator: SimpleUser;
  supervisor: SimpleUser;
}
