<script lang="ts" setup>
import { ref } from 'vue';
import type { Company } from '@/types/company/company';

const { documents } = defineProps<{ documents: Company['documents'] }>();
const zoomVisible = ref(false);
const zoomImage = ref('');

const openZoom = (filePath: string) => {
    zoomImage.value = filePath;
    zoomVisible.value = true;
};

const getStatusTag = (status: string): { severity: string; label: string } => {
    const normalized = status.toLowerCase();

    switch (normalized) {
        case 'aprovado':
            return { severity: 'success', label: 'Aprovado' };
        case 'pendente':
            return { severity: 'info', label: 'Pendente' };
        case 'rejeitado':
            return { severity: 'danger', label: 'Rejeitado' };
        default:
            return { severity: 'secondary', label: status };
    }
};
</script>

<template>
    <div class="card p-4 border rounded-lg shadow-sm bg-white border-gray-200 dark:border-gray-700 mt-6">
        <div class="font-semibold text-xl mb-4 text-gray-800 dark:text-gray-100 flex items-center">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mr-3">
                <i class="pi pi-file text-green-600 dark:text-green-400 text-sm"></i>
            </div>
            Documentos
        </div>

        <div class="max-h-96 overflow-y-auto">
            <DataTable v-if="documents && documents.length > 0" :value="documents" tableStyle="min-width: 100%">
                <Column field="type.name" header="Tipo" />
                <Column field="number" header="Número" />
                <Column header="Visualizar">
                    <template #body="slotProps">
                        <i class="pi pi-image text-green-600 cursor-pointer text-xl" @click="openZoom(`${slotProps.data.filePath}`)"></i>
                    </template>
                </Column>
                <Column field="description" header="Descrição" />
                <Column header="Status">
                    <template #body="slotProps">
                        <Tag :value="getStatusTag(slotProps.data.status).label" :severity="getStatusTag(slotProps.data.status).severity" />
                    </template>
                </Column>
            </DataTable>
            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="pi pi-file text-4xl mb-2"></i>
                <p>Nenhum documento cadastrado</p>
            </div>
        </div>

        <Dialog v-model:visible="zoomVisible" modal header="Visualização da Imagem" :style="{ width: '50vw' }">
            <img :src="zoomImage" alt="Imagem em zoom" class="w-full h-auto rounded border" />
        </Dialog>
    </div>
</template>
