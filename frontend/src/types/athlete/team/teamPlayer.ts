export interface TeamPlayer {
    id: number;
    number?: number;
    role: 'jogador' | 'capitao' | 'treinador';
    status: 'pendente' | 'ativo' | 'rescindido';
    joinedAt?: string;
    leftAt?: string | null;
    createdAt: string;
    updatedAt: string;
    user: {
        uuid: string;
        name: string;
        phone: string;
        imagePath?: string | null;
        athleteProfile?: {
            position: string;
            dominantSide: string;
        };
    };
    displayRole: string;
    displayStatus: string;
    isActive: boolean;
    daysInTeam?: number;
}

export interface PayloadTeamPlayerData {
    user_phone?: string;
    public_id?: string;
    number?: number;
    role?: 'jogador' | 'capitao' | 'treinador';
    status?: 'pendente' | 'ativo' | 'rescindido';
    joined_at?: string;
    left_at?: string;
}

export interface TeamPlayersResponse {
    status: string;
    message: string;
    data: TeamPlayer[];
}

export interface TeamPlayerResponse {
    status: string;
    message: string;
    data: TeamPlayer;
}
