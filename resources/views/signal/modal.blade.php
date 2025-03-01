<!-- Modal -->
<div id="modal" class="grand-box">
    <div class="petit-box">
        <button class="close-btn">&times;</button> <!-- Close button -->
        <h1>Entrer le type de déchet</h1>
        <form action="{{ route('signal.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="types-debris">
                <div class="buttons-container">
                    @foreach($wasteTypes as $wasteType)
                        <button type="button" class="btn-option" data-waste-type-id="{{ $wasteType->id }}">{{ $wasteType->name }}</button>
                        <div class="sub-types" id="subTypes{{ $wasteType->id }}">
                            @if($wasteType->specificWasteTypes && $wasteType->specificWasteTypes->count() > 0)
                                @foreach($wasteType->specificWasteTypes as $child)
                                    <button type="button" class="sub-type-btn" data-waste-type-id="{{ $child->id }}">{{ $child->name }}</button>
                                @endforeach
                            @else
                                <p>No sub-types found.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="waste_types[]" id="selectedWasteTypes">
            <!-- Section Localisation de Déchets -->
            <div class="mt-4">
                <h2 class="text-center mb-3">Localisation de Déchets</h2>
                <div class="main-box">
                    <div id="map" class="mb-3">
                        <p>Cliquez là-bas</p>
                    </div>
                    <div class="input-section">
                        <button type="button" id="autoLocationBtn" class="btn btn-primary mb-2">Utiliser ma localisation automatique</button>
                        <p class="text-center mb-2">ou</p>
                        <input type="text" id="manualLocationInput" class="form-control mb-2" placeholder="Entrez votre adresse manuellement" />
                        <button type="button" id="submitLocationBtn" class="btn btn-success">Valider</button>
                        <p id="locationStatus" class="text-center mt-2"></p>
                        <p class="loading text-center" id="loadingStatus">Chargement...</p>
                    </div>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="location" id="location">
                </div>
            </div>
            <div class="container mt-5">
                <h1 class="text-center titre">Ajouter les images de déchets</h1>
                <div class="row justify-content-center align-items-center">
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary control-btn prev-btn"><i class="fas fa-angle-left"></i></button>
                    </div>
                    <div class="col-8">
                        <div id="images-wrapper" class="d-flex justify-content-center">
                            <div class="image-container">
                                <p>Cliquer là-bas</p>
                                <i class="fas fa-circle-xmark delete-btn"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary control-btn next-btn"><i class="fas fa-angle-right"></i></button>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <input type="file" id="fileInput" name="media[]" accept="image/*,video/*" style="display: none;" multiple>
                    <button type="button" class="btn btn-info ajout-img">Ajouter image</button>
                </div>
            </div>
            <div class="container mt-5">
                <h1 class="mb-4">La quantité estimée</h1>
                <p class="QE mb-3 mt-4 fs-7 text-center petit-para">Entrez le volume de déchets que vous avez trouvé</p>
                <input type="text" class="form-control mb-4" name="estimated_volume" placeholder="Entrez le volume">
                <div class="description-container">
                    <label for="description" class="form-label fw-bold">Description :</label>
                    <textarea class="form-control mb-4" name="description" id="description" placeholder="Entrez une description pour l'image..." rows="4"></textarea>
                    <div class="d-flex justify-content-end gap-3">
                        <button type="button" id="annuler" class="btn btn-annuler">Annuler</button>
                        <button type="submit" id="continuer" class="btn btn-continuer">Continuer</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>