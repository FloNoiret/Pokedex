<!-- Header -->
<?php
        require("./layout/header.php"); ?>

<body>
    <!-- navbar -->
    <?php require("./layout/navbar.php") ?>

    <?php
    require("./PokemonsManager.php");
    require("./TypesManager.php");
    require("./ImagesManager.php");

    $pokemonManager = new PokemonsManager();
    $typeManager = new TypesManager();
    $imagesManager = new ImagesManager();

    // Get pokemon id
    $pokemon = $pokemonManager->get($_GET["id"]);
    $types = $typeManager->getAll();
    $error = null;

    try {
        if ($_POST) {
            $number = $_POST["number"];
            $name = $_POST["name"];
            $description = $_POST["description"];
            $idType1 = $_POST["type1"];
            $idType2 = $_POST["type2"] === "null" ? null : $_POST["type2"];
            // Verifications data entry
            if ($number < 1 || $number > 1000 || strlen($name) < 4 || strlen($name) > 40 || strlen($description) < 10 || strlen($description) > 200 || $idType1 < 1 || $idType1 > 25) {
                throw new Exception("Les données saisies ne sont pas correctes...");
            } else {
                try {
                    if ($_FILES["image"]["size"] < 2000000) {

                        $fileName = $_FILES["image"]["name"];
                        if (!is_dir("upload/")) {
                            mkdir("upload/");
                        }
                        $targetFile = "upload/{$fileName}";
                        $fileExtension = pathinfo($targetFile, PATHINFO_EXTENSION);
                        define("EXTENSIONS", ["png", "jpeg", "jpg", "webp"]);

                        if (in_array(strtolower($fileExtension), EXTENSIONS)) {
                            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                                $imagesManager = new ImagesManager();
                                $image = new Image(["name" => $fileName, "path" => $targetFile]);
                                $imagesManager->create($image);
                            } else {
                                throw new Exception("Une erreur est survenue...");
                            }
                        } else {
                            throw new Exception("L'extension du fichier n'est pas correcte.");
                        }
                    } else {
                        throw new Exception("Le fichier soumis est trop important");
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
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
            $pokemonManager->update($newPokemon);
            header("Location: index.php");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    ?>

    <main class="container">
        <?php
        if ($error) {
            echo "<p class='alert alert-danger'>$error</p>";
        } ?>

        <!-- Form with pre-established values-->
        <form method="post" enctype="multipart/form-data">
            <label for="number" class="form-label">Numéro</label>
            <input type="number" min=1 max=1000 name="number" value="<?= $pokemon->getNumber() ?>" placeholder="Le numéro du Pokémon" id="number" class="form-control" min=1 max=901>
            <label for="name" class="form-label">Nom</label>
            <input type="text" minlength="4" maxlength="40" name="name" value="<?= $pokemon->getName() ?>" placeholder="Le nom du Pokémon" id="name" class="form-control" minlength="3" maxlength="40">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="6" placeholder="La description du Pokémon" minlength="10" maxlength="200"><?= $pokemon->getDescription() ?></textarea>
            <label for="type1" class="form-label">Type 1</label>

            <select name="type1" id="type1" class="form-select">
                <option value="">--</option>
                <?php foreach ($types as $type) : ?>
                    <option <?= $type->getId() === $pokemon->getType1() ? "selected" : "" ?> value="<?= $type->getId() ?>"><?= $type->getName() ?></option>
                <?php endforeach ?>
            </select>

            <label for="type2" class="form-label">Type 2</label>

            <select name="type2" id="type2" class="form-select">
                <option value="null">--</option>
                <?php foreach ($types as $type) : ?>
                    <option <?= $type->getId() === $pokemon->getType2() ? "selected" : "" ?> value="<?= $type->getId() ?>"><?= $type->getName() ?></option>
                <?php endforeach ?>
            </select>

            <br />
            <label for="image" class="form-label">Image</label>
            <input type="file" required name="image" id="image" class="form-control">

            <img class="my-3 w-25" src="<?= $imagesManager->get($pokemon->getImage())->getPath() ?>" alt="<?= $pokemon->getName() ?>">
            <br />
            <input type="submit" class="btn btn-warning my-3" value="Modifer">
        </form>
    </main>
    <!-- footer -->
    <?php require("./layout/footer.php") ?>
</body>

</html>