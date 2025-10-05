export interface WordpressRootResponse {
  name: string;
  description: string;
  url: string;
  home: string;
  namespaces: string[];
}

export interface WordpressSettings {
  title: string;
  description: string;
  timezone_string: string;
  date_format: string;
  start_of_week: number;
}

export interface WpUser {
  id: number;
  name: string;
  email?: string;
  roles?: string[];
}
