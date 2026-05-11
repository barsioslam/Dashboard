<?php

// TransferController.php

namespace App\Controllers;

use App\Views\Genfile;

class TransferController {

    public function index() {
        $page['title'] = '$lang::transfer/index/title';
        $page['description'] = '$lang::transfer/index/description';
        $page['csslist'] = ['home/navbar'];
        $page['jspreloadlist'] = [];
        $page['jspostloadlist'] = ['home/navbar'];
        $page['navbar'] = "home";
        new Genfile('transfer/index', $page);
    }

    public function list() {
        $files = scandir(UPLOAD_PATH);

        for ($i = 0; $i < count($files); $i++) {
            if ($files[$i] == "." || $files[$i] == "..") {
                array_splice($files, $i, 1);
                $i--;
            }
        }
        
        $page['title'] = '$lang::transfer/list/title';
        $page['description'] = '$lang::transfer/list/description';
        $page['csslist'] = ['home/navbar', 'forms', 'tables'];
        $page['jspreloadlist'] = ['deleteFile'];
        $page['jspostloadlist'] = ['home/navbar', 'uploadFile'];
        $page['navbar'] = "home";
        new Genfile('transfer/list', $page, ["files" => $files]);
    }

    public function download($file) {

        $uploadDir = UPLOAD_PATH;

        // Vérifier que le paramètre est présent
        if (!isset($file)) {
            http_response_code(400);
            echo "Erreur : aucun fichier demandé.";
            exit;
        }

        // Sécurisation -> supprimer les chemins suspects (../)
        $filename = basename($file);
        $filePath = $uploadDir . $filename;

        // Vérifier existence
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo "(".$filePath.")";
            echo "Erreur : fichier introuvable.";
            exit;
        }

        // Forcer le téléchargement
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Length: ' . filesize($filePath));
        header('Pragma: public');
        header('Cache-Control: must-revalidate');

        // Nettoyage du tampon
        ob_clean();
        flush();

        // Lire le fichier par blocs
        $fp = fopen($filePath, 'rb');
        while (!feof($fp)) {
            echo fread($fp, 8192);
            flush();
        }
        fclose($fp);
        exit;

    }

}

?>