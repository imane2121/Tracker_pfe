@extends('layouts.app')

@section('content')
<main class="main">
    <section id="thank-you-section" class="starter-section">
        <div class="signContainer" style="max-width: 600px; margin: 2rem auto; padding: 0 1rem;">
            <div class="thank-you-card" style="background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); padding: 2.5rem; text-align: center;">
                <!-- Success Icon -->
                <div class="success-icon" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745; animation: scaleIn 0.5s ease-out;"></i>
                </div>

                <!-- Thank You Message -->
                <h1 style="color: #2c3e50; font-size: 2.2rem; margin-bottom: 1rem; font-weight: 600;">
                    Merci pour votre signalement!
                </h1>

                <!-- Success Message -->
                <div class="message-container" style="margin: 1.5rem 0; color: #5d6778;">
                    <p style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 0.5rem;">
                        Votre signalement a été soumis avec succès.
                    </p>
                    <p style="font-size: 1rem; color: #6c757d;">
                        Notre équipe examinera votre soumission dans les plus brefs délais.
                    </p>
                </div>

                <!-- Impact Message -->
                <div class="impact-message" style="background: #f8f9fa; border-radius: 10px; padding: 1.2rem; margin: 1.5rem 0;">
                    <p style="color: #2c3e50; margin: 0;">
                        <i class="fas fa-heart" style="color: #dc3545; margin-right: 0.5rem;"></i>
                        Votre contribution aide à préserver nos océans!
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons" style="margin-top: 2rem;">
                    <a href="{{ route('signal.create') }}" class="primary-button" style="
                        display: inline-block;
                        background-color: #3498db;
                        color: white;
                        padding: 0.8rem 1.5rem;
                        border-radius: 8px;
                        text-decoration: none;
                        font-weight: 500;
                        margin-right: 1rem;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-plus-circle"></i>
                        Nouveau Signalement
                    </a>
                    <a href="{{ route('signal.index') }}" class="secondary-button" style="
                        display: inline-block;
                        background-color: #f8f9fa;
                        color: #2c3e50;
                        padding: 0.8rem 1.5rem;
                        border-radius: 8px;
                        text-decoration: none;
                        font-weight: 500;
                        transition: all 0.3s ease;
                    ">
                        <i class="fas fa-list"></i>
                        Voir Tous les Signalements
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.primary-button:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
}

.secondary-button:hover {
    background-color: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

@media (max-width: 576px) {
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .action-buttons a {
        margin: 0;
        text-align: center;
    }

    .thank-you-card {
        padding: 1.5rem;
    }
}
</style>
@endsection 