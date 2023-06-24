import {SimpleUser} from '@/onboardingPlugin/models';

export interface Comment {
  id: number;
  body: string;
  user?: SimpleUser;
  replies: Comment[];
}
