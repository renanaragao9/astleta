<script lang="ts" setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import type { Company } from '@/types/company/company';
import qrcode from 'qrcode-generator';

interface Props {
    company: Company | null;
}

const props = defineProps<Props>();
const toast = useToast();

const qrCanvas = ref<HTMLCanvasElement>();
const qrSize = ref(200);

const baseUrl = computed(() => {
    return window.location.origin;
});

const profileUrl = computed(() => {
    if (!props.company) return '';
    return `${baseUrl.value}/compania/${props.company.id}`;
});

const generateQRCode = () => {
    if (!qrCanvas.value || !profileUrl.value) return;

    try {
        const qr = qrcode(0, 'M');
        qr.addData(profileUrl.value);
        qr.make();

        const canvas = qrCanvas.value;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        ctx.clearRect(0, 0, qrSize.value, qrSize.value);

        const moduleCount = qr.getModuleCount();
        const cellSize = qrSize.value / moduleCount;

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, qrSize.value, qrSize.value);

        ctx.fillStyle = '#000000';
        for (let row = 0; row < moduleCount; row++) {
            for (let col = 0; col < moduleCount; col++) {
                if (qr.isDark(row, col)) {
                    ctx.fillRect(col * cellSize, row * cellSize, cellSize, cellSize);
                }
            }
        }
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao gerar QR Code',
            life: 3000
        });
    }
};

const copyToClipboard = async () => {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(profileUrl.value);
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = profileUrl.value;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy');
            textArea.remove();
        }
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'URL copiada para a área de transferência',
            life: 3000
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao copiar URL. Tente copiar manualmente.',
            life: 3000
        });
    }
};

const printQRCode = () => {
    if (!qrCanvas.value) return;

    const canvas = qrCanvas.value;
    const dataURL = canvas.toDataURL();

    const printWindow = window.open('', '_blank');
    if (!printWindow) return;

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Code - ${props.company?.name}</title>
            <style>
                body {
                    margin: 0;
                    padding: 40px;
                    font-family: Arial, sans-serif;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    background: white;
                }
                .qr-container {
                    text-align: center;
                    border: 3px solid #333;
                    padding: 40px;
                    border-radius: 15px;
                    background: white;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    max-width: 500px;
                    width: 100%;
                }
                img {
                    display: block;
                    margin: 20px auto;
                    width: 400px;
                    height: 400px;
                    border: 2px solid #ccc;
                    border-radius: 10px;
                }
                h1 {
                    margin: 0 0 10px 0;
                    color: #333;
                    font-size: 28px;
                    font-weight: bold;
                }
                h2 {
                    margin: 0 0 20px 0;
                    color: #555;
                    font-size: 20px;
                }
                p {
                    margin: 10px 0;
                    color: #666;
                    font-size: 14px;
                }
                .logo {
                    width: 200px;
                    height: 200px;
                    object-fit: contain;
                    margin: 20px auto 10px;
                    border-radius: 10px;
                }
                @media print {
                    body { margin: 0; padding: 20px; }
                    .qr-container { border: 2px solid #000; padding: 30px; }
                    img { width: 400px; height: 400px; }
                }
            </style>
        </head>
        <body>
            <div class="qr-container">
                <h2>${props.company?.name}</h2>
                <img src="${dataURL}" alt="QR Code" />
                <p>Escaneie o QR code para acessar o perfil da empresa</p>
                <div class="instructions">
                    Astleta 
                </div>
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
};

const downloadQRCode = () => {
    if (!qrCanvas.value) return;

    try {
        const canvas = qrCanvas.value;
        const link = document.createElement('a');
        link.download = `qrcode-${props.company?.name || 'empresa'}.png`;
        link.href = canvas.toDataURL();
        link.click();

        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'QR Code baixado com sucesso',
            life: 3000
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao baixar QR Code',
            life: 3000
        });
    }
};

watch(
    () => props.company,
    () => {
        if (props.company) {
            generateQRCode();
        }
    },
    { immediate: true }
);

onMounted(() => {
    if (props.company) {
        generateQRCode();
    }
});
</script>

<template>
    <div class="card p-4 border rounded-lg shadow-sm bg-white border-gray-200 dark:border-gray-700">
        <div class="font-semibold text-xl mb-4 text-gray-800 dark:text-gray-100 flex items-center">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mr-3">
                <i class="pi pi-qrcode text-green-600 dark:text-green-400 text-sm"></i>
            </div>
            QR Code do Perfil
        </div>

        <div class="flex flex-col items-center space-y-4">
            <div class="qr-code-wrapper bg-white p-4 rounded-lg border" id="qr-code-for-print">
                <canvas ref="qrCanvas" :width="qrSize" :height="qrSize" class="border border-gray-300 rounded qr-canvas"></canvas>
                <div class="text-center mt-2">
                    <p class="text-sm text-gray-600">{{ company?.name }}</p>
                    <p class="text-xs text-gray-500">Escaneie para ver o perfil</p>
                </div>
            </div>

            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> URL do Perfil: </label>
                <div class="flex items-center space-x-2">
                    <input v-model="profileUrl" type="text" readonly class="flex-1 p-2 border border-gray-300 rounded-md bg-gray-50 text-sm" />
                    <Button @click="copyToClipboard" icon="pi pi-copy" severity="secondary" size="small" v-tooltip="'Copiar URL'" />
                </div>
            </div>

            <div class="flex space-x-2">
                <Button @click="printQRCode" icon="pi pi-print" label="Imprimir QR Code" severity="primary" />
                <Button @click="downloadQRCode" icon="pi pi-download" label="Download" severity="secondary" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.qr-code-container {
    width: 100%;
    max-width: 400px;
}

.qr-code-wrapper {
    background: white !important;
}

.qr-canvas {
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
}

@media print {
    .qr-code-container {
        max-width: none;
        width: 100%;
    }
    .qr-canvas {
        width: 400px !important;
        height: 400px !important;
    }
}
</style>
