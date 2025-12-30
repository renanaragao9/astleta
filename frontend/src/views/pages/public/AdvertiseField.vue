<script setup lang="ts">
import { useHead } from '@vueuse/head';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputMask from 'primevue/inputmask';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';
import { ref, reactive } from 'vue';
import { useToast } from 'primevue/usetoast';
import { usePreCompaniesRegistrationStore } from '@/stores/public/preCompaniesRegistrationStore';
import type { PreCompaniesRegistrationRequest } from '@/types/public/preCompaniesRegistrationType';
import PublicFooter from '@/components/public/PublicFooter.vue';
import PublicTopbar from '@/components/public/PublicTopbar.vue';

const landingShare = {
    url: 'https://seuracha.com/anunciar-campo',
    title: 'SeuRacha | Gestão e Reservas para Society',
    text: 'Aumente a ocupação e a receita do seu society com reservas online, comandas digitais e controle financeiro em um só lugar.'
};

useHead({
    title: 'Sistema para Gestão de Society e Arenas | SeuRacha',
    meta: [
        {
            name: 'description',
            content: 'Aumente a ocupação em até 38% e cresça o faturamento com reservas online, comandas digitais e controle financeiro completo para societies e arenas.'
        },
        {
            name: 'keywords',
            content:
                'sistema para society, sistema para arena esportiva, gestão de campos de futebol, software para quadra esportiva, sistema de reservas para society, ERP esportivo, gerenciar campo de futebol, sistema para dono de society, comanda digital, controle financeiro arena'
        },
        { property: 'og:title', content: 'Sistema para Gestão de Society e Arenas | SeuRacha' },
        {
            property: 'og:description',
            content: 'Aumente ocupação, vendas de bar e controle financeiro com o SeuRacha. Reservas online, comandas digitais e dashboards em um único painel.'
        },
        { property: 'og:type', content: 'website' },
        { property: 'og:url', content: landingShare.url },
        { property: 'og:image', content: 'https://seuracha.com/share/og-anunciar-campo.jpg' },
        { name: 'twitter:card', content: 'summary_large_image' },
        { name: 'twitter:title', content: 'Sistema para Donos de Society | SeuRacha' },
        {
            name: 'twitter:description',
            content: 'Software completo para gerenciar society, quadras e arenas esportivas.'
        },
        { name: 'twitter:image', content: 'https://seuracha.com/share/og-anunciar-campo.jpg' }
    ],
    link: [{ rel: 'canonical', href: landingShare.url }]
});

const preRegistrationStore = usePreCompaniesRegistrationStore();
const toast = useToast();

const showPreRegistrationModal = ref(false);

const formData = reactive<PreCompaniesRegistrationRequest>({
    name: '',
    email: '',
    phone: '',
    description: ''
});

const formErrors = ref<Record<string, string | string[]>>({});

const openPreRegistrationModal = () => {
    showPreRegistrationModal.value = true;
    clearForm();
};

const closePreRegistrationModal = () => {
    showPreRegistrationModal.value = false;
    clearForm();
};

const clearForm = () => {
    formData.name = '';
    formData.email = '';
    formData.phone = '';
    formData.description = '';
    formErrors.value = {};
    preRegistrationStore.clearError();
};

const validateForm = (): boolean => {
    formErrors.value = {};

    if (!formData.name.trim()) {
        formErrors.value.name = 'Nome é obrigatório';
    }

    if (!formData.email.trim()) {
        formErrors.value.email = 'Email é obrigatório';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
        formErrors.value.email = 'Email deve ter um formato válido';
    }

    if (!formData.phone.trim()) {
        formErrors.value.phone = 'Telefone é obrigatório';
    }

    return Object.keys(formErrors.value).length === 0;
};

const submitForm = async () => {
    if (!validateForm()) return;

    const result = await preRegistrationStore.register(formData);

    if (result.success) {
        closePreRegistrationModal();
        toast.add({
            severity: 'success',
            summary: 'Pré-cadastro enviado!',
            detail: result.message,
            life: 5000
        });
    } else {
        if (preRegistrationStore.error?.errors) {
            formErrors.value = preRegistrationStore.error.errors;
        }
    }
};

const features = [
    {
        icon: 'pi pi-calendar-plus',
        title: 'Reservas com disponibilidade real',
        description: 'Agenda inteligente com checagem automática, cálculo de preço por horário e extra-hour opcional.'
    },
    {
        icon: 'pi pi-whatsapp',
        title: 'Envie reservas em 1 clique',
        description: 'Compartilhe por WhatsApp, email ou sistema e confirme o pagamento presencial ou online.'
    },
    {
        icon: 'pi pi-receipt',
        title: 'Comandas conectadas à reserva',
        description: 'Itens de bar vinculados ao jogo, com status da comanda e fechamento seguro por forma de pagamento.'
    },
    {
        icon: 'pi pi-warehouse',
        title: 'Estoque, compras e fornecedores',
        description: 'Controle de produtos, armazéns, notas de compra e reposição rápida para não perder venda.'
    },
    {
        icon: 'pi pi-chart-line',
        title: 'Relatório financeiro unificado',
        description: 'Receitas, despesas, taxas e projeções em um dashboard exportável para sócios e contabilidade.'
    },
    {
        icon: 'pi pi-trophy',
        title: 'Torneios e times',
        description: 'Cadastre torneios públicos ou privados, times inscritos e pontuação para engajar a comunidade.'
    }
];

const testimonials = [
    {
        text: 'Desde que cadastrei meu campo na SeuRacha, minha ocupação aumentou 80%. A plataforma é fantástica!',
        author: 'Carlos Silva',
        role: 'Proprietário - Arena Sports'
    },
    {
        text: 'O sistema de reservas automatizado me poupou muito tempo. Agora posso focar em melhorar a infraestrutura.',
        author: 'Maria Santos',
        role: 'Proprietário - Campo Verde'
    },
    {
        text: 'A organização das reservas melhorou muito meu controle financeiro. Recomendo para todos os proprietários de campo.',
        author: 'João Oliveira',
        role: 'Proprietário - Arena Clube'
    }
];

const faqs = [
    {
        question: 'Quanto custa para anunciar meu campo?',
        answer: 'Você paga apenas 1.8% por reserva concluída. Sem mensalidade e sem multa para sair.'
    },
    {
        question: 'Como funciona o sistema de reservas?',
        answer: 'Os clientes podem ver a disponibilidade em tempo real e fazer reservas online. O pagamento é feito diretamente no seu estabelecimento.'
    },
    {
        question: 'Posso controlar os horários disponíveis?',
        answer: 'Sim! Você tem controle total sobre horários, preços e disponibilidade através do painel administrativo.'
    },
    {
        question: 'E se eu já tenho clientes fixos?',
        answer: 'Perfeito! Você pode bloquear horários para seus clientes regulares e disponibilizar o restante na plataforma.'
    },
    {
        question: 'Preciso de equipamentos especiais?',
        answer: 'Não! Você só precisa de um smartphone ou computador com acesso à internet. Nossa plataforma web funciona em qualquer dispositivo.'
    }
];

const statistics = [
    { number: '100+', label: 'Tipos de campos*' },
    { number: '1.8%', label: 'por reserva concluída' },
    { number: '95%', label: 'Satisfação dos Proprietários' },
    { number: '24h', label: 'Suporte Disponível' }
];

const performanceHighlights = [
    { value: '+38%', label: 'ocupação média em 90 dias', detail: 'Resultado médio dos campos que ativam reservas online e bloqueios inteligentes.' },
    { value: '2.4x', label: 'mais vendas de bar', detail: 'Ticket médio sobe com comandas digitais ligadas às reservas.' },
    { value: '12min', label: 'tempo salvo por reserva', detail: 'Automação de confirmação, cobrança e comunicação com o time.' },
    { value: '1.8%', label: 'por reserva', detail: 'Sem mensalidade. Cobramos somente quando a reserva é concluída.' }
];

const roiBullets = [
    'Calendário público que aparece no Google em menos de 7 dias',
    'Dashboard de metas semanais com alertas de ocupação baixa',
    'Comandas conectadas à reserva para reduzir calote e perda de produtos',
    'Relatórios exportáveis para sócios e contabilidade em um clique'
];

const shareLanding = async () => {
    try {
        if (typeof navigator !== 'undefined' && navigator.share) {
            await navigator.share({ title: landingShare.title, text: landingShare.text, url: landingShare.url });
            return;
        }

        if (typeof navigator !== 'undefined' && navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(landingShare.url);
            toast.add({ severity: 'info', summary: 'Link copiado', detail: 'Envie para sócios ou parceiros agora.', life: 3500 });
            return;
        }

        toast.add({ severity: 'warn', summary: 'Compartilhe', detail: landingShare.url, life: 3500 });
    } catch {
        toast.add({ severity: 'warn', summary: 'Não foi possível compartilhar', detail: landingShare.url, life: 4000 });
    }
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <PublicTopbar />

        <section class="bg-gradient-to-br from-primary-50 to-white py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 leading-tight">Sistema Completo para Gestão de <span class="text-primary-500">Society e Arenas Esportivas</span></h1>
                            <p class="text-xl text-gray-600 leading-relaxed">Junte-se à maior plataforma de reserva de campos esportivos do país e maximize seus ganhos com tecnologia de ponta.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <Button @click="openPreRegistrationModal" class="!bg-primary-500 !border-primary-500 !text-white !px-8 !py-4 !text-lg font-semibold hover:!bg-primary-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="pi pi-plus mr-2"></i>
                                Cadastrar Meu Campo
                            </Button>
                            <Button
                                as="a"
                                href="https://wa.me/5585921674573"
                                target="_blank"
                                class="!bg-green-500 !border-green-500 !text-white !px-8 !py-4 !text-lg font-semibold hover:!bg-green-600 transition-all duration-300 shadow-lg hover:shadow-xl"
                            >
                                <i class="pi pi-whatsapp mr-2"></i>
                                Entre em contato pelo WhatsApp
                            </Button>
                        </div>
                        <p class="text-center text-sm text-gray-600 mt-4 italic">Dúvidas? Nossa equipe está pronta para te guiar passo a passo!</p>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 pt-8">
                            <div v-for="stat in statistics" :key="stat.label" class="text-center">
                                <div class="text-2xl lg:text-3xl font-bold text-primary-500">{{ stat.number }}</div>
                                <div class="text-sm text-gray-600 mt-1">{{ stat.label }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="relative bg-white rounded-2xl shadow-2xl p-8 transform rotate-2">
                            <div class="absolute -top-4 -left-4 w-20 h-20 bg-primary-500 rounded-full flex items-center justify-center">
                                <i class="pi pi-shield text-white text-2xl"></i>
                            </div>
                            <div class="mt-8 space-y-4">
                                <h3 class="text-xl font-semibold text-gray-900">Painel de Controle</h3>
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                        <h4 class="text-lg font-semibold text-gray-800">Receitas Totais</h4>
                                        <div class="text-3xl font-bold text-green-600 mt-2">R$ 12.450</div>
                                        <div class="text-sm text-gray-600 mt-1">Valor mensal</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                        <h4 class="text-lg font-semibold text-gray-800">Despesas</h4>
                                        <div class="text-3xl font-bold text-red-600 mt-2">R$ 2.100</div>
                                        <div class="text-sm text-gray-600 mt-1">Valor mensal</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                        <h4 class="text-lg font-semibold text-gray-800">Taxas do Sistema</h4>
                                        <div class="text-3xl font-bold text-blue-600 mt-2">1.8%</div>
                                        <div class="text-sm text-gray-600 mt-1">Por reserva concluída. Sem mensalidade.</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                                        <h4 class="text-lg font-semibold text-gray-800">Saldo</h4>
                                        <div class="text-3xl font-bold text-green-600 mt-2">R$ 9.605</div>
                                        <div class="text-sm text-gray-600 mt-1">Balanço mensal</div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-semibold text-gray-900">Calendário de Reservas</h3>
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                    <div class="grid grid-cols-7 gap-2 text-center text-sm font-medium text-gray-600 mb-4">
                                        <div>Dom</div>
                                        <div>Seg</div>
                                        <div>Ter</div>
                                        <div>Qua</div>
                                        <div>Qui</div>
                                        <div>Sex</div>
                                        <div>Sáb</div>
                                    </div>
                                    <div class="grid grid-cols-7 gap-2">
                                        <div
                                            v-for="day in 31"
                                            :key="day"
                                            class="h-8 w-8 flex items-center justify-center rounded-full text-sm cursor-pointer hover:bg-primary-100 transition-colors"
                                            :class="day === 15 || day === 22 ? 'bg-primary-500 text-white' : 'text-gray-700'"
                                        >
                                            {{ day }}
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-gray-600">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-primary-500 rounded-full"></div>
                                            <span>Dias com reservas</span>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-semibold text-gray-900">Informações da Empresa</h3>
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-500">Nome</label>
                                            <div class="text-base text-gray-800">Arena Sports Club</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-500">CNPJ</label>
                                            <div class="text-base text-gray-800">12.345.678/0001-90</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-500">Telefone</label>
                                            <div class="text-base text-gray-800">(85) 99999-9999</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-500">Responsável</label>
                                            <div class="text-base text-gray-800">João Silva</div>
                                        </div>
                                    </div>
                                    <div class="flex justify-center">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                                            <div class="text-center text-sm text-gray-600 mb-2">QR Code para Reservas</div>
                                            <div class="w-20 h-20 bg-gray-200 flex items-center justify-center mx-auto">
                                                <i class="pi pi-qrcode text-2xl text-gray-500"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative overflow-hidden bg-slate-900 text-white px-4 sm:px-6 lg:px-8 py-16">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 via-emerald-400/10 to-transparent"></div>
            <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="absolute -left-12 bottom-0 h-56 w-56 rounded-full bg-white/5 blur-3xl"></div>

            <div class="relative max-w-7xl mx-auto grid lg:grid-cols-[1.2fr_0.8fr] gap-12 items-center">
                <div class="space-y-6">
                    <p class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 text-emerald-100 text-sm font-semibold backdrop-blur">
                        <i class="pi pi-bolt mr-2"></i>
                        Resultados em 90 dias ou menos
                    </p>
                    <h2 class="text-3xl lg:text-5xl font-bold leading-tight text-white">Ganhe tração real com dados e automação</h2>
                    <p class="text-lg text-slate-100/80 max-w-2xl">Mostramos o que importa: mais ocupação, mais vendas de bar e menos tempo perdido com confirmação manual.</p>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="item in performanceHighlights" :key="item.label" class="rounded-xl border border-white/10 bg-white/5 p-4 shadow-lg">
                            <div class="text-2xl font-bold text-emerald-300">{{ item.value }}</div>
                            <div class="text-sm font-semibold text-white">{{ item.label }}</div>
                            <p class="text-sm text-slate-100/70 mt-2">{{ item.detail }}</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="rounded-xl bg-white text-slate-900 p-5 shadow-2xl">
                            <h3 class="text-lg font-semibold mb-3">O que você passa a ganhar</h3>
                            <ul class="space-y-2 text-sm text-slate-700">
                                <li v-for="point in roiBullets" :key="point" class="flex items-start gap-2">
                                    <i class="pi pi-check-circle text-emerald-600 mt-1"></i>
                                    <span>{{ point }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-5 shadow-xl">
                            <h3 class="text-lg font-semibold text-white mb-3">Pronto para compartilhar</h3>
                            <p class="text-sm text-slate-100/80">Envie o link do seu campo para sócios ou parceiros e comece a receber reservas em minutos.</p>
                            <div class="flex flex-wrap gap-3 mt-4">
                                <Button @click="shareLanding" class="!bg-white !text-emerald-600 !border-white">
                                    <i class="pi pi-share-alt mr-2"></i>
                                    Compartilhar link
                                </Button>
                                <Button as="a" href="https://wa.me/5585921674573?text=Quero+colocar+meu+campo+no+SeuRacha" target="_blank" class="!bg-emerald-500 !border-emerald-400 !text-white">
                                    <i class="pi pi-whatsapp mr-2"></i>
                                    Falar com especialista
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative rounded-2xl bg-white/5 border border-white/10 p-8 shadow-2xl backdrop-blur">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-sm text-slate-100/70">Mapa de receita</p>
                            <h3 class="text-2xl font-bold text-white">Previsão semanal</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-200 text-xs font-semibold">Live</span>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-100/80">Ocupação média</span>
                            <span class="text-xl font-semibold text-emerald-300">82%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-emerald-400 to-emerald-300" style="width: 82%"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-100/80">Faturamento previsto</span>
                            <span class="text-xl font-semibold text-white">R$ 14.200</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-white/5 border border-white/10 p-3">
                                <p class="text-xs text-slate-100/70">Bar / Comanda</p>
                                <p class="text-lg font-semibold text-white">R$ 4.800</p>
                                <p class="text-xs text-emerald-200">+2.1x vs último mês</p>
                            </div>
                            <div class="rounded-lg bg-white/5 border border-white/10 p-3">
                                <p class="text-xs text-slate-100/70">Reservas online</p>
                                <p class="text-lg font-semibold text-white">R$ 9.400</p>
                                <p class="text-xs text-emerald-200">+31% com calendário público</p>
                            </div>
                        </div>
                        <div class="rounded-lg bg-white text-slate-900 p-4 shadow-xl mt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold">Slot mais rentável</span>
                                <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-700">Sexta 20h</span>
                            </div>
                            <p class="text-sm text-slate-700 mt-2">R$ 420,00 de ticket médio e fila de espera de 6 times.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="text-center py-8 bg-gray-50">
            <Button @click="openPreRegistrationModal" class="!bg-primary-500 !border-primary-500 !text-white !px-8 !py-4 !text-lg font-semibold hover:!bg-primary-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                Sou Dono de Arena, Quero Testar
            </Button>
        </div>

        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6">Sistema de Gestão para Donos de Society e Arenas</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Oferecemos tudo que você precisa para transformar seu campo em um negócio próspero e bem-sucedido.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <Card v-for="feature in features" :key="feature.title" class="h-full !border-0 !shadow-lg hover:!shadow-xl transition-all duration-300 hover:-translate-y-2">
                        <template #content>
                            <div class="text-center space-y-4 p-4">
                                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                                    <i :class="feature.icon" class="text-primary-500 text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ feature.title }}</h3>
                                <p class="text-gray-600 leading-relaxed">{{ feature.description }}</p>
                            </div>
                        </template>
                    </Card>
                </div>

                <div class="text-center mt-12">
                    <Button @click="openPreRegistrationModal" class="!bg-primary-500 !border-primary-500 !text-white !px-8 !py-4 !text-lg font-semibold hover:!bg-primary-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                        Quero Gerenciar Meu Society
                    </Button>
                </div>
            </div>
        </section>

        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6">Plataforma Completa de Gestão</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Gerencie seus campos e reservas de forma integrada e eficiente, com ferramentas avançadas para otimizar seu negócio.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-calendar-plus text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Disponibilidade em tempo real</h3>
                        <p class="text-gray-600">Agenda por dia da semana, checagem de conflito e cálculo automático de preço e hora extra.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-send text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Envio de reservas</h3>
                        <p class="text-gray-600">Confirme e envie reservas por WhatsApp, email ou sistema com histórico e status.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-receipt text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Comanda ligada ao jogo</h3>
                        <p class="text-gray-600">Itens de bar por reserva, fechamento por forma de pagamento e envio do recibo.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-warehouse text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Estoque e compras</h3>
                        <p class="text-gray-600">Produtos, armazéns, notas de compra e fornecedores para não faltar item no bar.</p>
                    </div>
                </div>

                <div class="mt-16 bg-white rounded-2xl p-8 shadow-lg">
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold text-gray-900">Gestão Integrada</h3>
                            <p class="text-gray-600">Nossa plataforma une a gestão de campos e reservas em uma única interface, facilitando o controle total do seu negócio esportivo.</p>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Cadastro detalhado de campos e instalações
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Sistema de reservas online e offline
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Controle de disponibilidade em tempo real
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Relatórios de ocupação e receita
                                </li>
                            </ul>
                        </div>
                        <div class="relative">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="text-center mb-4">
                                    <i class="pi pi-calendar text-4xl text-primary-500"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Calendário de Reservas</h4>
                                <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                    <div class="font-medium text-gray-500">D</div>
                                    <div class="font-medium text-gray-500">S</div>
                                    <div class="font-medium text-gray-500">T</div>
                                    <div class="font-medium text-gray-500">Q</div>
                                    <div class="font-medium text-gray-500">Q</div>
                                    <div class="font-medium text-gray-500">S</div>
                                    <div class="font-medium text-gray-500">S</div>
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs bg-gray-200">1</div>
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs bg-gray-200">2</div>
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs bg-primary-500 text-white">3</div>
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs bg-gray-200">4</div>
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs bg-primary-500 text-white">5</div>
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs bg-gray-200">6</div>
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs bg-gray-200">7</div>
                                </div>
                                <div class="mt-4 text-xs text-gray-600">
                                    <div class="flex items-center justify-center space-x-4">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-primary-500 rounded-full mr-1"></div>
                                            <span>Reservado</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-gray-200 rounded-full mr-1"></div>
                                            <span>Disponível</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6">Sistema de Comanda Digital para Campos de Futebol</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Maximize seus lucros com nosso sistema completo de vendas integrado ao seu campo esportivo.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-receipt text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Gestão de Comandas</h3>
                        <p class="text-gray-600">Crie e gerencie comandas digitais para cada reserva, facilitando o controle de vendas.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-chart-bar text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Relatórios de Vendas</h3>
                        <p class="text-gray-600">Acompanhe vendas por período, produto e status da comanda com dashboards detalhados.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-send text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Envio Automático</h3>
                        <p class="text-gray-600">Envie comandas por email ou sistema para clientes, com recibos e comprovantes.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-tags text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Produtos Integrados</h3>
                        <p class="text-gray-600">Cadastre produtos com preços e quantidades para adicionar facilmente às comandas.</p>
                    </div>
                </div>

                <div class="mt-16 bg-gray-50 rounded-2xl p-8">
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold text-gray-900">Aumente sua Receita em até 40%</h3>
                            <p class="text-gray-600">Com o sistema de comanda digital, você não perde vendas e tem controle total sobre seus produtos e lucros.</p>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Criação rápida de comandas por reserva
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Controle preciso de produtos vendidos
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Envio automático de recibos por email
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Relatórios detalhados de vendas
                                </li>
                            </ul>
                        </div>
                        <div class="relative">
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <div class="text-center mb-4">
                                    <i class="pi pi-receipt text-4xl text-primary-500"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Comanda Digital</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700">Refrigerante</span>
                                        <span class="font-bold text-primary-500">R$ 5,00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700">Salgadinho</span>
                                        <span class="font-bold text-primary-500">R$ 8,00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700">Cerveja</span>
                                        <span class="font-bold text-primary-500">R$ 12,00</span>
                                    </div>
                                    <hr class="my-2" />
                                    <div class="flex justify-between items-center font-bold">
                                        <span class="text-gray-900">Total</span>
                                        <span class="text-primary-500">R$ 25,00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="text-center py-8">
            <Button @click="openPreRegistrationModal" class="!bg-primary-500 !border-primary-500 !text-white !px-8 !py-4 !text-lg font-semibold hover:!bg-primary-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                Cadastrar Meu Campo Agora
            </Button>
        </div>

        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6">Controle Financeiro para Arenas Esportivas e Quadras</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Gerencie todas as suas despesas e receitas de forma organizada e tenha controle total sobre suas finanças.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-plus-circle text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Cadastro de Despesas/Receitas</h3>
                        <p class="text-gray-600">Registre entradas e saídas com categorias, valores e datas de vencimento.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-filter text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Filtros Avançados</h3>
                        <p class="text-gray-600">Filtre por categoria, tipo, status e período para análises precisas.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-chart-line text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Relatórios Financeiros</h3>
                        <p class="text-gray-600">Visualize relatórios detalhados de entradas, saídas e saldos.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-file-export text-primary-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Exportação de Dados</h3>
                        <p class="text-gray-600">Exporte seus dados financeiros em CSV para análises externas.</p>
                    </div>
                </div>

                <div class="mt-16 bg-gray-50 rounded-2xl p-8 shadow-lg">
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold text-gray-900">Gestão Financeira Integrada</h3>
                            <p class="text-gray-600">Mantenha suas finanças organizadas com controle completo de despesas e receitas, facilitando tomadas de decisão.</p>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Categorização de despesas e receitas
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Controle de status (pago/pendente)
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Datas de vencimento e criação
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Interface intuitiva e responsiva
                                </li>
                            </ul>
                        </div>
                        <div class="relative">
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <div class="text-center mb-4">
                                    <i class="pi pi-wallet text-4xl text-primary-500"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Controle de Despesas</h4>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-4 gap-2 text-xs font-medium text-gray-500 mb-2">
                                        <div>Nome</div>
                                        <div>Tipo</div>
                                        <div>Valor</div>
                                        <div>Status</div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-sm py-2 border-b border-gray-100">
                                        <div class="font-medium">Aluguel</div>
                                        <div><span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Saída</span></div>
                                        <div class="font-medium">R$ 1.200,00</div>
                                        <div><span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pendente</span></div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-sm py-2 border-b border-gray-100">
                                        <div class="font-medium">Vendas</div>
                                        <div><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Entrada</span></div>
                                        <div class="font-medium">R$ 850,00</div>
                                        <div><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Pago</span></div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-sm py-2">
                                        <div class="font-medium">Manutenção</div>
                                        <div><span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Saída</span></div>
                                        <div class="font-medium">R$ 350,00</div>
                                        <div><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Pago</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="text-center py-8 bg-white">
            <Button @click="openPreRegistrationModal" class="!bg-primary-500 !border-primary-500 !text-white !px-8 !py-4 !text-lg font-semibold hover:!bg-primary-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                Quero Controlar Meu Financeiro
            </Button>
        </div>

        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6">Relatório Financeiro Completo</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Dashboard financeiro completo com visão detalhada de receitas, despesas, projeções e análise de performance.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-dollar text-blue-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Receitas do Período</h3>
                        <p class="text-2xl font-bold text-blue-600">R$ 12.450,00</p>
                        <p class="text-gray-600">Total de entradas realizadas</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-minus-circle text-red-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Despesas do Período</h3>
                        <p class="text-2xl font-bold text-red-600">R$ 3.200,00</p>
                        <p class="text-gray-600">Total de saídas realizadas</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Saldo do Período</h3>
                        <p class="text-2xl font-bold text-green-600">R$ 9.250,00</p>
                        <p class="text-gray-600">Lucro líquido do período</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-times-circle text-purple-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Cancelamentos</h3>
                        <p class="text-2xl font-bold text-purple-600">R$ 450,00</p>
                        <p class="text-gray-600">Valor total de cancelamentos</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-percentage text-blue-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Taxa de Conversão</h3>
                        <p class="text-2xl font-bold text-blue-600">87.5%</p>
                        <p class="text-gray-600">Reservas concluídas vs total</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-dollar text-green-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Valor Médio</h3>
                        <p class="text-2xl font-bold text-green-600">R$ 85,00</p>
                        <p class="text-gray-600">Por reserva/comanda</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-chart-bar text-green-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Margem de Lucro</h3>
                        <p class="text-2xl font-bold text-green-600">74.3%</p>
                        <p class="text-gray-600">Saldo vs receitas totais</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="pi pi-chart-bar text-orange-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Receita Potencial</h3>
                        <p class="text-2xl font-bold text-orange-600">R$ 15.200,00</p>
                        <p class="text-gray-600">Realizadas + projetadas</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-8 shadow-lg">
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold text-gray-900">Análise Financeira Detalhada</h3>
                            <p class="text-gray-600">Visualize todas as transações em tempo real com filtros avançados, relatórios exportáveis e métricas de performance.</p>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Dashboard com indicadores-chave
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Filtros por período e categoria
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Relatórios de receitas e despesas
                                </li>
                                <li class="flex items-center">
                                    <i class="pi pi-check text-green-500 mr-2"></i>
                                    Exportação em CSV
                                </li>
                            </ul>
                        </div>
                        <div class="relative">
                            <div class="bg-white rounded-xl shadow-lg p-6">
                                <div class="text-center mb-4">
                                    <i class="pi pi-chart-line text-4xl text-primary-500"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Transações Financeiras</h4>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-4 gap-2 text-xs font-medium text-gray-500 mb-2">
                                        <div>Tipo</div>
                                        <div>Descrição</div>
                                        <div>Valor</div>
                                        <div>Status</div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-sm py-2 border-b border-gray-100">
                                        <div><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Reserva</span></div>
                                        <div class="font-medium">Campo Principal - João Silva</div>
                                        <div class="font-medium text-green-600">R$ 120,00</div>
                                        <div><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Concluída</span></div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-sm py-2 border-b border-gray-100">
                                        <div><span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Comanda</span></div>
                                        <div class="font-medium">Comanda #00123</div>
                                        <div class="font-medium text-green-600">R$ 85,00</div>
                                        <div><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Fechada</span></div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-sm py-2 border-b border-gray-100">
                                        <div><span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Despesa</span></div>
                                        <div class="font-medium">Manutenção do Gramado</div>
                                        <div class="font-medium text-red-600">R$ 350,00</div>
                                        <div><span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pendente</span></div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 text-sm py-2">
                                        <div><span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Taxa</span></div>
                                        <div class="font-medium">Taxa do Sistema</div>
                                        <div class="font-medium text-blue-600">1.8% por reserva</div>
                                        <div><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Sem mensalidade</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6">Como Funciona</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">Processo simples e rápido para começar a receber reservas hoje mesmo.</p>
                </div>

                <div class="grid lg:grid-cols-3 gap-12">
                    <div class="text-center space-y-4">
                        <div class="w-20 h-20 bg-primary-500 rounded-full flex items-center justify-center mx-auto text-white text-2xl font-bold">1</div>
                        <h3 class="text-2xl font-semibold text-gray-900">Cadastre seu Campo</h3>
                        <p class="text-gray-600">Adicione fotos, descrição e defina seus horários disponíveis através do nosso sistema intuitivo.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-20 h-20 bg-primary-500 rounded-full flex items-center justify-center mx-auto text-white text-2xl font-bold">2</div>
                        <h3 class="text-2xl font-semibold text-gray-900">Receba Reservas</h3>
                        <p class="text-gray-600">Atletas encontram seu campo, fazem reservas online e pagam diretamente no seu estabelecimento.</p>
                    </div>
                    <div class="text-center space-y-4">
                        <div class="w-20 h-20 bg-primary-500 rounded-full flex items-center justify-center mx-auto text-white text-2xl font-bold">3</div>
                        <h3 class="text-2xl font-semibold text-gray-900">Receba seus Ganhos</h3>
                        <p class="text-gray-600">Acompanhe suas reservas em tempo real e receba os pagamentos diretamente no seu estabelecimento.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6">O que Nossos Parceiros Dizem</h2>
                    <p class="text-xl text-gray-600">Conheça as histórias de sucesso de quem já faz parte da nossa família.</p>
                </div>

                <div class="grid lg:grid-cols-3 gap-8">
                    <Card v-for="testimonial in testimonials" :key="testimonial.author" class="!border-0 !shadow-lg h-full">
                        <template #content>
                            <div class="space-y-6 p-4">
                                <div class="flex text-primary-500">
                                    <i class="pi pi-star-fill" v-for="n in 5" :key="n"></i>
                                </div>
                                <p class="text-gray-700 italic leading-relaxed">"{{ testimonial.text }}"</p>
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                        <i class="pi pi-user text-primary-500"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ testimonial.author }}</div>
                                        <div class="text-sm text-gray-600">{{ testimonial.role }}</div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </section>

        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6">Perguntas Frequentes</h2>
                    <p class="text-xl text-gray-600">Esclarecemos as principais dúvidas sobre nossa plataforma.</p>
                </div>

                <Accordion :activeIndex="0">
                    <AccordionPanel v-for="(faq, index) in faqs" :key="index" :value="index">
                        <AccordionHeader>{{ faq.question }}</AccordionHeader>
                        <AccordionContent>
                            <p class="text-gray-700 leading-relaxed">{{ faq.answer }}</p>
                        </AccordionContent>
                    </AccordionPanel>
                </Accordion>
            </div>
        </section>

        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-primary-500 to-primary-600">
            <div class="max-w-4xl mx-auto text-center">
                <div class="space-y-8">
                    <h2 class="text-3xl lg:text-5xl font-bold text-white">Pronto para Começar?</h2>
                    <p class="text-xl text-primary-100 max-w-2xl mx-auto">Entre em contato conosco agora e transforme seu campo em um negócio de sucesso. Nossa equipe está pronta para ajudar você!</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Button @click="openPreRegistrationModal" class="!bg-white !border-white !text-primary-500 !px-8 !py-4 !text-lg font-semibold hover:!bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="pi pi-plus mr-2"></i>
                            Cadastrar Agora
                        </Button>
                        <Button @click="shareLanding" class="!bg-primary-700 !border-primary-700 !text-white !px-8 !py-4 !text-lg font-semibold hover:!bg-primary-800 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="pi pi-share-alt mr-2"></i>
                            Enviar para meu sócio
                        </Button>
                    </div>
                </div>
            </div>
        </section>
        <section class="sr-only">
            <h2>Sistema para Gerenciar Society e Campos de Futebol</h2>
            <p>O SeuRacha é um sistema online desenvolvido para donos de society, arenas esportivas, quadras de futebol, futsal e espaços esportivos que desejam automatizar reservas, controlar o financeiro e aumentar a ocupação dos horários.</p>
        </section>
        <PublicFooter />
    </div>

    <Dialog v-model:visible="showPreRegistrationModal" modal header="Cadastre seu Campo" :style="{ width: '32rem' }" :breakpoints="{ '960px': '75vw', '640px': '95vw' }" :closable="true">
        <form @submit.prevent="submitForm" class="space-y-6">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2"> Nome do Estabelecimento <span class="text-red-500">*</span></label>
                    <InputText id="name" v-model="formData.name" placeholder="Ex: Arena Sports Club" class="w-full" :class="{ 'p-invalid': formErrors.name }" />
                    <small v-if="formErrors.name" class="text-red-500">{{ formErrors.name }}</small>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2"> Email <span class="text-red-500">*</span> </label>
                    <InputText id="email" v-model="formData.email" type="email" placeholder="seu@email.com" class="w-full" :class="{ 'p-invalid': formErrors.email }" />
                    <small v-if="formErrors.email" class="text-red-500">{{ formErrors.email }}</small>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2"> Telefone <span class="text-red-500">*</span></label>
                    <InputMask id="phone" v-model="formData.phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full" :class="{ 'p-invalid': formErrors.phone }" type="tel" />
                    <small v-if="formErrors.phone" class="text-red-500">{{ formErrors.phone }}</small>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2"> Descrição (opcional) </label>
                    <Textarea id="description" v-model="formData.description" placeholder="Conte-nos mais sobre seu campo..." rows="3" class="w-full" />
                </div>
            </div>

            <div v-if="preRegistrationStore.error?.message" class="text-red-500 text-sm">
                {{ preRegistrationStore.error.message }}
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <Button type="button" @click="closePreRegistrationModal" label="Cancelar" class="!bg-gray-200 !border-gray-200 !text-gray-700 hover:!bg-gray-300" />
                <Button type="submit" :loading="preRegistrationStore.isLoading" label="Enviar Pré-Cadastro" class="!bg-primary-500 !border-primary-500 !text-white hover:!bg-primary-600" />
            </div>
        </form>
    </Dialog>

    <Toast />
</template>

<style scoped>
.text-primary-500 {
    color: #10b981;
}

.bg-primary-500 {
    background-color: #10b981;
}

.bg-primary-600 {
    background-color: #059669;
}

.bg-primary-700 {
    background-color: #047857;
}

.bg-primary-800 {
    background-color: #065f46;
}

.bg-primary-50 {
    background-color: #ecfdf5;
}

.bg-primary-100 {
    background-color: #d1fae5;
}

.text-primary-100 {
    color: #d1fae5;
}

.border-primary-500 {
    border-color: #10b981;
}

.border-primary-700 {
    border-color: #047857;
}

.border-primary-800 {
    border-color: #065f46;
}

.hover\:bg-primary-600:hover {
    background-color: #059669;
}

/* Classes para seção de atletas */
.bg-blue-50 {
    background-color: #eff6ff;
}

.bg-blue-100 {
    background-color: #dbeafe;
}

.bg-blue-500 {
    background-color: #3b82f6;
}

.bg-blue-600 {
    background-color: #2563eb;
}

.text-blue-500 {
    color: #3b82f6;
}

.text-blue-800 {
    color: #1e40af;
}

.hover\:bg-blue-600:hover {
    background-color: #2563eb;
}

.border-blue-500 {
    border-color: #3b82f6;
}

.text-green-500 {
    color: #10b981;
}

.ml-13 {
    margin-left: 3.25rem;
}

.transition-all {
    transition: all 0.3s ease;
}

:deep(.p-card) {
    border-radius: 1rem;
}

:deep(.p-accordion .p-accordion-tab .p-accordion-header .p-accordion-header-link) {
    padding: 1.25rem;
    font-weight: 600;
}

:deep(.p-accordion .p-accordion-tab .p-accordion-content) {
    padding: 1.25rem;
}

:deep(.p-button) {
    border-radius: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

:deep(.p-button:hover) {
    transform: translateY(-2px);
}
</style>
