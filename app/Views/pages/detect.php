<div class="container-fluid m-2">
    <div class="row">
        <div class="col-2">
            <ul class="list-group">
                <li class="list-group-item active" aria-current="true">bild1.jpg</li>
                <li class="list-group-item">bild2.jpg</li>
                <li class="list-group-item">bild3.jpg</li>
            </ul>
        </div>
        <div class="col-6">
            <div id="canvas"></div>

        </div>
        <div class="col-4">
            <ul class="list-group mb-2" id="partlist">

            </ul>

            <button type="button" class="btn btn-primary" id="addBox">
                Add Box
            </button>
            <hr></hr>
            <div class="d-flex justify-content-evenly">
                <button type="button" class="btn btn-success">
                    Create Invoice
                </button>
                <div class="form-check pt-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                        Save as training data
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>



<template id="hitboxItem">
    <li class="list-group-item">
        <div class="row">
            <div class="col align-self-center">
                <select class="form-select" style="border-width:3px">
                    <option value="0">Muendungsabschluss</option>
                    <option value="1">Regenhaube</option>
                    <option value="2">Dachdurchfuehrung_gerade</option>
                </select>
            </div>
            <div  class="col">
                <button type="button-sm" class="btn btn-outline-danger float-end">
                    Delete
                </button>
            </div>
        </div>
    </li>
</template>

<script type="text/javascript" src="detection_script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/p5.min.js" integrity="sha512-N4kV7GkNv7QR7RX9YF/olywyIgIwNvfEe2nZtfyj73HdjCUkAfOBDbcuJ/cTaN04JKRnw1YG1wnUyNKMsNgg3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>