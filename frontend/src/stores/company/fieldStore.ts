import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Field, FieldResponse, FieldPayload } from '@/types/company/field';
import type { FieldType } from '@/types/company/select/fieldType';
import type { FieldSurface } from '@/types/company/select/fieldSurface';
import type { FieldSize } from '@/types/company/select/fieldSize';
import type { Filters } from '@/types/global/filter';
import { FieldService } from '@/services/company/fieldService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useFieldStore = defineStore('field', () => {
    const fields = ref<Field[]>([]);
    const field = ref<Field>({
        id: 0,
        name: '',
        description: '',
        pricePerHour: 0,
        extraHourPrice: 0,
        fieldTypeId: 0,
        fieldSurfaceId: 0,
        fieldSizeId: 0,
        isActive: true,
        isAllowsExtraHour: false,
        created: '',
        imagePath: '',
        fieldType: {} as FieldType,
        fieldSurface: {} as FieldSurface,
        fieldSize: {} as FieldSize,
        selectedItemIds: [],
        schedules: [],
        fieldImages: []
    });

    const selectedFields = ref<Field[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasFields = computed(() => fields.value.length > 0);
    const getFieldById = computed(() => (id: number) => {
        return fields.value.find((field) => field.id === id);
    });

    async function fetchFields(filters: Partial<Filters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: FieldResponse = await FieldService.getFields(filters);
            fields.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar campos');
            fields.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchField(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await FieldService.getField(id);
            field.value = response.data;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar campo');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createField(fieldData: FieldPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await FieldService.createField(fieldData);
            fields.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar campo');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateField(id: number, fieldData: Partial<FieldPayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await FieldService.updateField(id, fieldData);
            const index = fields.value.findIndex((field) => field.id === id);
            if (index !== -1) {
                fields.value[index] = response.data;
            }
            if (field.value.id === id) {
                field.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar campo');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteField(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await FieldService.deleteField(id);
            fields.value = fields.value.filter((field) => field.id !== id);
            selectedFields.value = selectedFields.value.filter((field) => field.id !== id);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar campo');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedFields() {
        if (selectedFields.value.length === 0) return;

        loading.value = true;
        error.value = null;

        try {
            const deletePromises = selectedFields.value.map((field) => (field.id ? FieldService.deleteField(field.id) : Promise.resolve()));

            await Promise.all(deletePromises);

            const selectedIds = selectedFields.value.map((field) => field.id);
            fields.value = fields.value.filter((field) => !selectedIds.includes(field.id));
            selectedFields.value = [];
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar campos selecionados');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateImage(id: number, image: File): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await FieldService.updateImage(id, image);
            const index = fields.value.findIndex((f) => f.id === id);
            if (index !== -1) {
                fields.value[index].imagePath = response.data.image_path;
            }
            if (field.value.id === id) {
                field.value.imagePath = response.data.image_path;
            }
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar imagem');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearSelectedFields() {
        selectedFields.value = [];
    }

    function clearField() {
        field.value = {
            id: 0,
            name: '',
            description: '',
            pricePerHour: 0,
            extraHourPrice: 0,
            fieldTypeId: 0,
            fieldSurfaceId: 0,
            fieldSizeId: 0,
            isActive: true,
            isAllowsExtraHour: false,
            created: '',
            imagePath: '',
            fieldType: {} as FieldType,
            fieldSurface: {} as FieldSurface,
            fieldSize: {} as FieldSize,
            selectedItemIds: [],
            schedules: [],
            fieldImages: []
        };
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        fields,
        field,
        selectedFields,
        loading,
        error,
        pagination,

        // Getters
        getFieldById,
        hasFields,

        // Actions
        fetchFields,
        fetchField,
        createField,
        updateField,
        deleteField,
        deleteSelectedFields,
        clearSelectedFields,
        clearField,
        clearError,
        updateImage
    };
});
