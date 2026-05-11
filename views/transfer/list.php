<div id="main">
    <div class="table">
        <div class="row">
            <div class="col flex-1">
                <form method="post" class="form" action="/<?= $_GET['lang']; ?>/ajax/fileTransfer" enctype="multipart/form-data" id="uploadForm">
                    <h2>Envoi de fichiers</h2>
                    <label for="fileInput" class="dropzone" id="dropzone">
                        Glissez-déposez ici<br>ou cliquez pour parcourir
                        <input type="file" id="fileInput" name="file" style="display: none;">
                    </label>

                    <button type="submit" class="btn" id="uploadBtn">Téléverser</button>

                    <div class="progress" id="progress"><span></span></div>
                    <div id="status"></div>
                </form>
            </div>
        </div>
        <?php
        if (empty($files)) { 
        ?>
        <div class="row">
            Aucun fichier.
        </div>
        <?php
        } else {
            foreach ($files as $file) {
        ?>
        <div class="row">
            <div class="col flex-1"><a href="/transfer/download/<?= $file; ?>" target="blank" style="color: white;"><?= $file; ?></a></div>
            <div class="col" onclick="delFile('<?= $_GET['lang']; ?>', '<?= $file; ?>');" style="background-color: red; color: white; cursor: pointer">&#10007;</div>
        </div>
        <?php
            }
        }
        ?>
    </div>
</div>