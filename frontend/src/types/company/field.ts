import type { FieldType } from '@/types/company/select/fieldType';
import type { FieldSurface } from '@/types/company/select/fieldSurface';
import type { FieldSize } from '@/types/company/select/fieldSize';
import type { FieldSchedule, FieldSchedulePayload } from '@/types/company/field/FieldSchedule';
import type { FieldImage, FieldImagePayload } from '@/types/company/field/FieldImage';

export interface Field {
    id: number;
    name: string;
    description: string;
    pricePerHour: number | null;
    extraHourPrice: number | null;
    fieldTypeId: number;
    fieldSurfaceId: number;
    fieldSizeId: number;
    isActive: boolean;
    isAllowsExtraHour: boolean;
    created: string;
    imagePath: string | File;
    fieldType: FieldType;
    fieldSurface: FieldSurface;
    fieldSize: FieldSize;
    selectedItemIds?: number[];
    schedules?: FieldSchedule[];
    fieldImages?: FieldImage[];
}

export interface FieldPayload {
    name: string;
    description?: string;
    price_per_hour: number;
    extra_hour_price?: number;
    field_type_id: number;
    field_surface_id: number;
    field_size_id: number;
    is_active: boolean;
    is_allows_extra_hour?: boolean;
    image_path?: string | File;
    item_ids?: number[];
    schedules?: FieldSchedulePayload[];
    field_images?: FieldImagePayload[];
}

export interface FieldResponse {
    data: Field[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        currentPage: number;
        from: number;
        lastPage: number;
        links: {
            url: string | null;
            label: string;
            page: number | null;
            active: boolean;
        }[];
        path: string;
        perPage: number;
        to: number;
        total: number;
    };
}
