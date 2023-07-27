<!-- Header -->
<?php
require("./layout/header.php"); ?>


<body>
    <!-- navbar -->
    <?php require("./layout/navbar.php") ?>

    <!-- form variables -->
    <?php
    require("./PokemonsManager.php");
    require("./TypesManager.php");
    require("./ImagesManager.php");

    $pokemonManager = new PokemonsManager();

    $typeManager = new TypesManager();
    $types = $typeManager->getAll();
    $error = null;


    if ($_POST) {
        $number = $_POST["number"];
        $name = $_POST["name"];
        $description = $_POST["description"];
        $idType1 = $_POST["type1"];
        $idType2 = $_POST["type2"] === "null" ? null : $_POST["type2"];

        try {
            // Check img size
            if ($_FILES["image"]["size"] < 2000000) {
                $imagesManager = new ImagesManager();
                $fileName = $_FILES["image"]["name"];
                if (!is_dir("upload/")) {
                    mkdir("upload/");
                }

                // Check extension img name
                $targetFile = "upload/{$fileName}";
                $fileExtension = pathinfo($targetFile, PATHINFO_EXTENSION);
                define("EXTENSIONS", ["png", "jpeg", "jpg", "webp"]);
                // upload img
                if (in_array(strtolower($fileExtension), EXTENSIONS)) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                        $imagesManager = new ImagesManager();
                        $image = new Image(["name" => $fileName, "path" => $targetFile]);
                        $imagesManager->create($image);

                        // Error message if problem is while upload
                    } else {
                        throw new Exception("Une erreur est survenue...");
                    }
                    // Error message if problem is the extension of the file
                } else {
                    throw new Exception("L'extension du fichier n'est pas correcte.");
                }
                // Error message if problem is the size of the file
            } else {
                throw new Exception("Le fichier soumis est trop important");
            }
            // Error message in its display variable
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        $idImage = $imagesManager->getLastImageId();
        $newPokemon = new Pokemon([
            "number" => $number,
            "name" => $name,
            "description" => $description,
            "type1" => $idType1,
            "type2" => $idType2,
            "image" => $idImage,
        ]);
        $pokemonManager->create($newPokemon);
        header("Location: index.php");
    }
    ?>

    <main class="container">
        <!-- Error Message with img problem exception info -->
        <?php
        if ($error) {
            echo "<p class='alert alert-danger'>$error</p>";
        } ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="number" class="form-label">Numéro</label>
            <input type="number" name="number" placeholder="Le numéro du Pokémon" id="number" class="form-control" min=1 max=901>
            <label for="name" class="form-label">Nom</label>
            <input type="text" name="name" placeholder="Le nom du Pokémon" id="name" class="form-control" minlength="3" maxlength="40">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="6" placeholder="La description du Pokémon" minlength="10" maxlength="200"></textarea>
            <label for="type1" class="form-label">Type 1</label>
            <select name="type1" id="type1" class="form-select">
                <option value="">--</option>
                <?php foreach ($types as $type) : ?>
                    <option value="<?= $type->getId() ?>"><?= $type->getName() ?></option>
                <?php endforeach ?>
            </select>

            <label for="type2" class="form-label">Type 2</label>

            <select name="type2" id="type2" class="form-select">
                <option value="null">--</option>
                <?php foreach ($types as $type) : ?>
                    <option value="<?= $type->getId() ?>"><?= $type->getName() ?></option>
                <?php endforeach ?>
            </select>
            <label for="image" class="form-label">Image</label>
            <input type="file" name="image" id="image" class="form-control">

            <input type="submit" class="btn btn-success mt-3" value="Créer">
        </form>
    </main>

    <!-- footer -->
    <?= require("./layout/footer.php") ?>
</body>

</html>