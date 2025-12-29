/**
 * Formata um número de telefone brasileiro adicionando parênteses, espaço e traço.
 * @param phone - O número de telefone a ser formatado (apenas números).
 * @returns O número de telefone formatado.
 */
export function formatPhone(phone: string): string {
    const cleaned = phone.replace(/\D/g, '');

    if (cleaned.length === 11) {
        // Celular: (XX) XXXXX-XXXX
        return cleaned.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (cleaned.length === 10) {
        // Fixo: (XX) XXXX-XXXX
        return cleaned.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        return phone;
    }
}
