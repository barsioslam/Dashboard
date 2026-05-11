<?php

// AjaxController.php

namespace App\Controllers;

use App\Views\Genfile;
use App\Utils\System\SystemStats;

class AjaxController {

    public function sysStats(): void {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(SystemStats::get());
        exit;
    }

    public function fileTransfer() {
        // Autoriser une réponse JSON
        header('Content-Type: application/json; charset=utf-8');

        // Vérifier qu'un fichier est reçu
        if(!isset($_FILES['file'])){
            echo json_encode([
                "success" => false,
                "error" => "Aucun fichier reçu."
            ]);
            exit;
        }

        // Dossier de stockage (à adapter)
        $uploadDir = UPLOAD_PATH;

        // Création si non existant
        if(!file_exists($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }

        $file = $_FILES['file'];
        $filename = basename($file['name']);

        // Normalisation du nom
        $filename = preg_replace('/\s+/', '_', $filename); // remplace espaces par _
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename); // supprime les caractères spéciaux

        $targetPath = $uploadDir . $filename;

        // Vérifier erreurs natives PHP
        if($file['error'] !== UPLOAD_ERR_OK){
            echo json_encode([
                "success" => false,
                "error" => "Erreur upload PHP : " . $file['error']
            ]);
            exit;
        }

        // Déplacer le fichier
        if(move_uploaded_file($file['tmp_name'], $targetPath)){
            if(file_exists($targetPath)){
                echo json_encode([
                    "success" => true,
                    "message" => "Upload réussi",
                    "filename" => $filename
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "error" => "Erreur : le fichier n’existe pas après la copie."
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "error" => "move_uploaded_file a échoué."
            ]);
        }

    }

    public function deleteFile() {
        if(isset($_POST['file'])) {
            $uploadDir = UPLOAD_PATH;

            $filepath = $uploadDir . basename($_POST['file']);

            // Création si non existant
            if(file_exists($filepath)){
                unlink($filepath);
            }
        }
        exit;
    }

}

?>