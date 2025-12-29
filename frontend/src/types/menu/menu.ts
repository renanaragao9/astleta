export interface MenuItem {
    label: string;
    icon?: string;
    to?: string | { name: string };
    url?: string;
    target?: string;
    class?: string;
    items?: MenuItem[];
    separator?: boolean;
}
