<script setup lang="ts">
import { computed } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

interface Props {
    visible: boolean;
    activeTab?: 'terms' | 'privacy';
}

interface Emits {
    (e: 'update:visible', value: boolean): void;
}

const props = withDefaults(defineProps<Props>(), {
    activeTab: 'terms'
});

const emit = defineEmits<Emits>();

const activeTabRef = computed(() => (props.activeTab === 'terms' ? '0' : '1'));

const handleClose = () => {
    emit('update:visible', false);
};
</script>

<template>
    <Dialog :visible="visible" @update:visible="handleClose" modal header="Termos de Uso e Política de Privacidade" :style="{ width: 'min(90vw, 900px)', maxHeight: '90vh' }" :closable="true">
        <div class="privacy-terms-content">
            <Tabs v-model:value="activeTabRef" class="w-full">
                <TabList>
                    <Tab value="0">Termos de Uso</Tab>
                    <Tab value="1">Política de Privacidade</Tab>
                </TabList>

                <TabPanels class="mt-4">
                    <TabPanel value="0" class="p-0">
                        <div class="max-h-96 overflow-y-auto pr-2 space-y-4">
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-surface-0 mb-4">Termos de Uso - SeuRacha</h3>

                            <div class="space-y-4 text-surface-700 dark:text-surface-300">
                                <section>
                                    <h4 class="font-semibold mb-2">1. Aceitação dos Termos</h4>
                                    <p>
                                        Ao acessar e usar a plataforma SeuRacha, você concorda em cumprir e estar vinculado aos seguintes termos e condições de uso, que, juntamente com nossa política de privacidade, regem o relacionamento da SeuRacha
                                        com você em relação a este site.
                                    </p>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">2. Descrição do Serviço</h4>
                                    <p>O SeuRacha é uma plataforma digital que conecta atletas e estabelecimentos esportivos, facilitando o agendamento de quadras esportivas e a organização de eventos esportivos. Nossos serviços incluem:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li>Busca e reserva de quadras esportivas</li>
                                        <li>Organização de grupos e eventos esportivos</li>
                                        <li>Sistema de avaliações e comentários</li>
                                        <li>Processamento de pagamentos</li>
                                    </ul>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">3. Elegibilidade e Cadastro</h4>
                                    <p>Para usar nossos serviços, você deve ter pelo menos 14 anos de idade. Ao criar uma conta, você garante que:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li>Todas as informações fornecidas são verdadeiras e precisas</li>
                                        <li>Você manterá a segurança de sua conta</li>
                                        <li>Você é responsável por todas as atividades em sua conta</li>
                                    </ul>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">4. Uso Aceitável</h4>
                                    <p>Você concorda em usar a plataforma apenas para fins legais e de acordo com estes termos. É proibido:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li>Usar a plataforma para qualquer atividade ilegal</li>
                                        <li>Enviar conteúdo ofensivo, difamatório ou inadequado</li>
                                        <li>Interferir no funcionamento da plataforma</li>
                                        <li>Tentar acessar contas de outros usuários</li>
                                    </ul>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">5. Pagamentos e Cancelamentos</h4>
                                    <p>Os pagamentos são processados através de nossa plataforma segura. As políticas de cancelamento variam conforme o estabelecimento e são claramente informadas no momento da reserva.</p>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">6. Propriedade Intelectual</h4>
                                    <p>Todo o conteúdo da plataforma SeuRacha, incluindo textos, gráficos, logotipos e software, é propriedade da SeuRacha ou de seus licenciadores e está protegido por leis de direitos autorais.</p>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">7. Limitação de Responsabilidade</h4>
                                    <p>A SeuRacha não será responsável por danos diretos, indiretos, incidentais ou consequenciais decorrentes do uso da plataforma. Nosso papel é facilitar a conexão entre usuários e estabelecimentos.</p>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">8. Modificações</h4>
                                    <p>Reservamo-nos o direito de modificar estes termos a qualquer momento. As alterações serão comunicadas através da plataforma e entrarão em vigor imediatamente após a publicação.</p>
                                </section>
                            </div>
                        </div>
                    </TabPanel>

                    <TabPanel value="1" class="p-0">
                        <div class="max-h-96 overflow-y-auto pr-2 space-y-4">
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-surface-0 mb-4">Política de Privacidade - SeuRacha</h3>

                            <div class="space-y-4 text-surface-700 dark:text-surface-300">
                                <section>
                                    <h4 class="font-semibold mb-2">1. Dados Coletados</h4>
                                    <p>Coletamos os seguintes tipos de informações:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li><strong>Dados pessoais:</strong> Nome completo, CPF, e-mail, telefone, data de nascimento, gênero</li>
                                        <li><strong>Dados de localização:</strong> Endereço para entrega de serviços e geolocalização para busca de quadras</li>
                                        <li><strong>Dados de pagamento:</strong> Informações de cartão de crédito/débito e histórico de transações</li>
                                        <li><strong>Dados de uso:</strong> Histórico de navegação, preferências, interações com a plataforma</li>
                                        <li><strong>Dados técnicos:</strong> Endereço IP, tipo de dispositivo, navegador, cookies</li>
                                        <li><strong>Dados de comunicação:</strong> Mensagens enviadas através da plataforma, avaliações e comentários</li>
                                    </ul>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">2. Finalidade do Uso dos Dados</h4>
                                    <p>Utilizamos seus dados para:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li><strong>Prestação de serviços:</strong> Processar reservas, facilitar pagamentos, conectar usuários</li>
                                        <li><strong>Melhoria da plataforma:</strong> Análise de uso, desenvolvimento de novas funcionalidades</li>
                                        <li><strong>Comunicação:</strong> Envio de confirmações, atualizações, notificações importantes</li>
                                        <li><strong>Marketing:</strong> Ofertas personalizadas, newsletters (com consentimento)</li>
                                        <li><strong>Segurança:</strong> Prevenção de fraudes, proteção da plataforma</li>
                                        <li><strong>Suporte:</strong> Atendimento ao cliente, resolução de problemas</li>
                                        <li><strong>Obrigações legais:</strong> Cumprimento de exigências fiscais e regulatórias</li>
                                    </ul>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">3. Compartilhamento de Dados</h4>
                                    <p>Seus dados podem ser compartilhados com:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li><strong>Estabelecimentos parceiros:</strong> Dados necessários para processamento de reservas</li>
                                        <li><strong>Processadores de pagamento:</strong> Para processar transações financeiras de forma segura</li>
                                        <li><strong>Provedores de serviços:</strong> Empresas que nos auxiliam na operação da plataforma (hospedagem, analytics)</li>
                                        <li><strong>Autoridades legais:</strong> Quando exigido por lei ou para proteção de direitos</li>
                                        <li><strong>Outros usuários:</strong> Informações públicas do perfil para facilitar interações</li>
                                    </ul>
                                    <p class="mt-2"><strong>Importante:</strong> Nunca vendemos seus dados pessoais para terceiros.</p>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">4. Tempo de Armazenamento</h4>
                                    <p>Mantemos seus dados pelo tempo necessário para:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li><strong>Conta ativa:</strong> Durante toda a vigência de sua conta</li>
                                        <li><strong>Dados de transação:</strong> Por 5 anos após a última transação (exigência legal)</li>
                                        <li><strong>Dados de marketing:</strong> Até a retirada do consentimento</li>
                                        <li><strong>Logs de segurança:</strong> Por 6 meses para análise de segurança</li>
                                        <li><strong>Dados anonimizados:</strong> Podem ser mantidos indefinidamente para pesquisas</li>
                                    </ul>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">5. Seus Direitos (LGPD)</h4>
                                    <p>Você tem o direito de:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li><strong>Acesso:</strong> Solicitar quais dados temos sobre você</li>
                                        <li><strong>Correção:</strong> Corrigir dados incorretos ou desatualizados</li>
                                        <li><strong>Exclusão:</strong> Solicitar a exclusão de seus dados (direito ao esquecimento)</li>
                                        <li><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</li>
                                        <li><strong>Oposição:</strong> Opor-se ao processamento para finalidades específicas</li>
                                        <li><strong>Revogação:</strong> Retirar consentimento a qualquer momento</li>
                                        <li><strong>Revisão:</strong> Solicitar revisão de decisões automatizadas</li>
                                    </ul>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">6. Segurança dos Dados</h4>
                                    <p>Implementamos medidas técnicas e organizacionais adequadas para proteger seus dados, incluindo:</p>
                                    <ul class="list-disc ml-6 mt-2 space-y-1">
                                        <li>Criptografia de dados em trânsito e em repouso</li>
                                        <li>Controles de acesso rigorosos</li>
                                        <li>Monitoramento contínuo de segurança</li>
                                        <li>Auditorias regulares de segurança</li>
                                        <li>Treinamento de equipe sobre proteção de dados</li>
                                    </ul>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">7. Cookies e Tecnologias Similares</h4>
                                    <p>Utilizamos cookies para melhorar sua experiência, analisar o uso da plataforma e personalizar conteúdo. Você pode gerenciar suas preferências de cookies nas configurações do seu navegador.</p>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">8. Transferência Internacional</h4>
                                    <p>Seus dados são processados no Brasil. Caso seja necessária transferência internacional, garantiremos proteção adequada conforme a LGPD.</p>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">9. Contato e DPO</h4>
                                    <p><strong>Encarregado de Proteção de Dados (DPO):</strong></p>
                                    <div class="bg-surface-100 dark:bg-surface-800 p-4 rounded-lg mt-2">
                                        <p><strong>Nome:</strong> Renan da Silva Aragão</p>
                                        <p><strong>E-mail:</strong> seuracha@seuracha.com</p>
                                    </div>
                                    <p class="mt-4">Para exercer seus direitos ou esclarecer dúvidas sobre esta política, entre em contato conosco através dos canais acima.</p>
                                </section>

                                <section>
                                    <h4 class="font-semibold mb-2">10. Alterações na Política</h4>
                                    <p>
                                        Esta política pode ser atualizada periodicamente. Notificaremos sobre alterações significativas através de e-mail ou notificação na plataforma. A versão mais atual estará sempre disponível em nossa plataforma.
                                    </p>
                                    <p class="mt-2"><strong>Última atualização:</strong> 20 de outubro de 2025</p>
                                </section>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>

        <template #footer>
            <div class="flex justify-end">
                <Button label="Fechar" @click="handleClose" />
            </div>
        </template>
    </Dialog>
</template>

<style scoped>
.privacy-terms-content :deep(.p-tabpanels) {
    padding: 0;
}

.privacy-terms-content :deep(.p-tabpanel) {
    padding: 0;
}

.privacy-terms-content h4 {
    color: var(--primary-color);
}

.privacy-terms-content section {
    border-left: 3px solid var(--primary-color-100);
    padding-left: 1rem;
    margin-left: 0.5rem;
}

.privacy-terms-content ul li {
    margin-bottom: 0.25rem;
}

.privacy-terms-content .max-h-96 {
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color-200) transparent;
}

.privacy-terms-content .max-h-96::-webkit-scrollbar {
    width: 6px;
}

.privacy-terms-content .max-h-96::-webkit-scrollbar-track {
    background: transparent;
}

.privacy-terms-content .max-h-96::-webkit-scrollbar-thumb {
    background-color: var(--primary-color-200);
    border-radius: 3px;
}
</style>
