export type ServerConfig = {
  adminPassword: string;
  adminUsername: string;
  enabled: boolean;
  host: string;
  lastSync: {
    date: string;
    timezone: string;
    timezone_type: number;
  } | null;
  overrideSalary: boolean;
  port: string;
  salaries: Array<Record<string, any>>;
  scheme: 'Http' | 'Https';
  syncInterval: number;
  syncing: boolean;
};

// Add this line to ensure TypeScript treats the file as a module
export {};
