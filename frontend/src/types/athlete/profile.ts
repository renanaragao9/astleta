import type { Sport } from '@/types/athlete/select/Sport';
import type { Position } from '@/types/athlete/select/Position';
import type { Feature } from '@/types/athlete/select/Feature';
import type { Skill } from '@/types/athlete/select/Skill';

export interface AthleteProfile {
    id: number;
    uuid: string;
    name: string;
    email: string;
    phone: string;
    date: string | null;
    gender: string;
    imagePath: string;
    lang: string;
    isPublic: boolean;
    createdAt: string;
    athleteProfile: {
        id: number;
        dominantSide: string;
        height: string;
        weight: string;
        bio: string;
        userId: number;
        sportId: number;
        positionId: number;
        subpositionId: number;
        featureId: number;
        subfeatureId: number;
        createdAt: string;
        updatedAt: string;
        deletedAt: null;
        sport: Sport;
        position: Position;
        subposition: {
            id: number;
            name: string;
        };
        feature: Feature;
        subfeature: {
            id: number;
            name: string;
        };
    };
    skills: Skill[];
    team?: {
        name: string;
        shieldPath: string;
    };
    statistics?: Record<
        string,
        {
            value: number;
            icon: string;
            color: string;
        }
    >;
    ratings?: {
        overallAverage: number;
        technicalAverage: number;
        tacticalAverage: number;
        physicalAverage: number;
        mentalAverage: number;
        teamworkAverage: number;
        totalRatings: number;
        recentRatings: Array<{
            rating: number;
            comment: string;
            bookingName: string;
            date: string;
        }>;
    };
}

export interface UserPayload {
    name: string;
    date: string;
    phone: string;
    gender: string;
    is_public?: boolean;
}

export interface AthleteProfilePayload {
    dominant_side?: string;
    height?: number;
    weight?: number;
    bio?: string;
    sport_id?: number;
    position_id?: number;
    subposition_id?: number;
    feature_id?: number;
    subfeature_id?: number;
    skill_ids?: number[];
}

export interface AthleteProfileResponse {
    data: AthleteProfile;
    message: string;
}
