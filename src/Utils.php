<?php


namespace App;


use Exception;

abstract class Utils
{
    /**
     * @param int $n
     * @return string
     * @throws Exception
     */
    private function randomString(int $n): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * @param string $directory
     * @return string
     * @throws Exception
     */
    private function uploadImage(string $directory): string
    {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            // Infos sur le fichier téléchargé
            $fileTmpPath = $_FILES['logo']['tmp_name'];
            $fileName = $_FILES['logo']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Changement du nom par quelque chose qui ne se répétera pas
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Les extensions autorisées
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

            if (!file_exists('./' .$directory . '/logos')) {
                mkdir('./' . $directory . '/logos');
            }

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = './' . $directory . '/logos/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    return $newFileName;
                } else {
                    throw new Exception('L\'image n\'a pas pu être téléchargée, les droits d\'écriture ne sont pas accordés');
                }
            } else {
                throw new Exception('L\'image n\'a pas pu être téléchargée, l\'extension doit être : ' . implode(',', $allowedfileExtensions));
            }
        } else {
            if (isset($_FILES['uploadedFile']['error'])) {
                throw new Exception('Il y eu a une erreur lors du téléchargement : ' . $_FILES['uploadedFile']['error']);
            } else {
                throw new Exception('Il y a une erreur dans le formulaire : enctype="multipart/form-data"');
            }
        }
    }
}