import { Type } from "@angular/core";

export interface Tab {
  id: string;
  label: string;
  component: Type<any>;
  data?: any;
  closable?: boolean;
}
