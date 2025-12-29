export interface TeamPlayerFilters {
    role?: 'jogador' | 'capitao' | 'treinador';
    status?: 'pendente' | 'ativo' | 'rescindido';
    search?: string;
    limit?: number;
    page?: number;
}
