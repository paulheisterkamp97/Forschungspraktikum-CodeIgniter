<div class="container-lg bg-light p-5">
    <h1 class="p-5">
        Upload center
    </h1>

    <div>
        <h3 class="m-4">Select to upload and process</h3>
        <?php if (isset($errors)): ?>
            <div class="text-danger">
                <?=  $errors->listErrors()?>
            </div>
        <?php endif; ?>
        <form method="post" action="<?=base_url('store-image')?>" enctype="multipart/form-data">
            <div class="form-group mb-4">
                <label class="form-label fs-5" for="image">Select Picture to upload</label>
                <input type="file" class="form-control form-control-lg" id="image" accept="image/jpeg,image/png" name="image" size="33" />
            </div>
            <div class="form-group mb-4">
                <label class="form-label fs-5" for="filename">Specify project name</label>
                <input type="text" id="filename"  class="form-control form-control-lg" name="filename"/>
            </div>
            <div class="form-group mb-4">
                <button type="submit" class="btn-lg btn-primary" >Upload Image</button>
            </div>
        </form>
    </div>
</div>

</div>