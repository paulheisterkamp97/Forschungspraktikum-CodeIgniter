
<div class="container-lg bg-light p-5">
        <h2>Congratulations, the image has successfully been uploaded and processed</h2>
        <p>
            <?php if (isset($errors)){
              echo $errors;
            }?>
        </p>

        <p>
            <?php echo anchor('upload', 'Go back to Image Upload'); ?>
        </p>
    </div>