/**
 * Formata um CNPJ adicionando pontos, barra e traço.
 * @param cnpj - O CNPJ a ser formatado (apenas números).
 * @returns O CNPJ formatado.
 */
export function formatCnpj(cnpj: string | null | undefined): string {
    if (!cnpj) {
        return '';
    }

    const cleaned = cnpj.replace(/\D/g, '');

    if (cleaned.length !== 14) {
        return cnpj;
    }

    return cleaned.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
}
