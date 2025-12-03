/**
 * Drinkly API TypeScript Types
 * 
 * Tyto typy lze použít ve frontendu (Next.js / React) pro type-safe API volání.
 * 
 * Base URL: http://drinkly.test/api/v1 (nebo https://your-domain.com/api/v1 v produkci)
 */

// ============================================================================
// Authentication Types
// ============================================================================

export interface RegisterRequest {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

export interface LoginRequest {
    email: string;
    password: string;
}

export interface AuthResponse {
    user: User;
    token: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
    updated_at: string;
}

// ============================================================================
// Water Intake Types
// ============================================================================

export interface WaterIntake {
    id: number;
    amount: number; // v mililitrech
    intake_time: string; // ISO 8601 format
}

export interface StoreWaterIntakeRequest {
    amount: number; // required, min: 1
    intake_time?: string; // optional, ISO 8601 format. Pokud není zadáno, použije se now()
}

export interface WaterIntakeListResponse {
    data: WaterIntake[];
    meta: {
        date: string; // YYYY-MM-DD
        total_amount: number; // součet za den v ml
    };
}

export interface WeeklyStatsResponse {
    week_start: string; // YYYY-MM-DD
    week_end: string; // YYYY-MM-DD
    total_amount: number; // součet za celý týden v ml
    daily_breakdown: Record<string, number>; // { "2025-12-01": 200, "2025-12-02": 300, ... }
}

// ============================================================================
// Error Types
// ============================================================================

export interface ValidationError {
    message: string;
    errors: Record<string, string[]>;
}

export interface UnauthorizedError {
    message: "Unauthenticated.";
}

// ============================================================================
// API Client Helper Types
// ============================================================================

export type ApiResponse<T> = T | ValidationError | UnauthorizedError;

/**
 * Příklad použití:
 * 
 * const response = await fetch('http://drinkly.test/api/v1/register', {
 *   method: 'POST',
 *   headers: { 'Content-Type': 'application/json' },
 *   body: JSON.stringify({ name, email, password, password_confirmation })
 * });
 * 
 * const data: AuthResponse = await response.json();
 * 
 * // Uložit token
 * localStorage.setItem('token', data.token);
 * 
 * // Použít token v dalších requestech
 * const waterResponse = await fetch('http://drinkly.test/api/v1/water-intake', {
 *   headers: {
 *     'Authorization': `Bearer ${data.token}`,
 *     'Content-Type': 'application/json'
 *   }
 * });
 */
