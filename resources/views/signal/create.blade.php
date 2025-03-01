@extends('layouts.app')
@include('signal.modal')
@section('content')
<div class="container my-5">
    <div class="row align-items-start">
        <div class="col-md-6 video-container d-flex justify-content-start">
            <video class="rounded shadow mb-4" autoplay loop muted playsinline controls>
                <source src="{{ asset('videos/La_Pollution_Marine.mp4') }}" type="video/mp4">
            </video>
        </div>
        <div class="col-md-6">
            <h2 class="text-dark mb-3">Signaler Un Déchet</h2>
            <p class="text-muted mb-4">Effectuer un signalement, cliquez sur le bouton ci-dessous.
                Votre demande sera prise en compte et traitée dans les plus brefs délais.</p>
            <div class="d-flex gap-3 justify-content-end">
                <button class="btn btn-outline-primary">Annuler</button>
                <button class="btn btn-primary" id="signalerBtn">Signaler</button>
            </div>
        </div>
    </div>
    <div class="mt-5 p-4 bg-white shadow rounded hover-bg-gray">
        <h4>Objectif de Signalement</h4>
        <p class="text-muted">Le signalement est une action essentielle pour maintenir la propreté de notre environnement.
            En signalant un déchet, vous contribuez à identifier les zones qui nécessitent une intervention rapide.
            Cela permet aux autorités compétentes de prendre les mesures nécessaires pour nettoyer et préserver notre espace commun.
            Votre participation active est cruciale pour assurer un environnement sain et agréable pour tous.
            Cliquez sur le bouton "Signaler" pour faire part d'un déchet que vous avez repéré. Votre contribution fait la différence !</p>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM fully loaded and parsed'); // Debugging line

        const signalerBtn = document.getElementById('signalerBtn');
        const modal = document.getElementById('modal');

        if (signalerBtn && modal) {
            console.log('Signaler button and modal found'); // Debugging line
            signalerBtn.addEventListener('click', function () {
                console.log('Signaler button clicked'); // Debugging line
                modal.style.display = 'block'; // Show the modal
            });

            // Optional: Add a close button inside the modal
            const closeBtn = modal.querySelector('.close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    modal.style.display = 'none'; // Hide the modal
                });
            }
        } else {
            console.error('Signaler button or modal not found!');
        }
    });
</script>
@endpush
@endsection