
<div>
        <h3>Congratulations, the image has successfully been uploaded</h3>
        <p>
            <?php if (isset($errors)){
              echo $errors;
            }?>


        </p>

        <p>
            <?php echo anchor('upload', 'Go back to Image Upload'); ?>
        </p>
    </div>