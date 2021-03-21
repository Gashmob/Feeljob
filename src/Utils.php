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

            // Dossier ou sera mise l'image
            $uploadFileDir = './uploads/' . $directory;
            // Changement du nom par quelque chose qui ne se répétera pas
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Les extensions autorisées
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir);
            }

            if (in_array($fileExtension, $allowedfileExtensions)) {
                if (substr($uploadFileDir, -1) != '/')
                    $uploadFileDir .= '/';
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

    /**
     * Return an array with 2 values
     * 'password' => your encrypted password
     * 'salt' => the salt used for encryption
     *
     * @param string $password
     * @return string[]
     * @throws Exception
     */
    public function passwordEncrypt(string $password): array
    {
        $salt = $this->randomString(16);

        return [
            'password' => password_hash(hash('sha512', $password . $salt), PASSWORD_BCRYPT, ['cost' => 12]),
            'salt' => $salt
        ];
    }

    /**
     * @param string $userPassword
     * @param string $userSalt
     * @param string $passwordToVerify
     * @return bool
     */
    public function passwordVerify(string $userPassword, string $userSalt, string $passwordToVerify): bool
    {
        return password_verify(hash('sha512', $passwordToVerify . $userSalt), $userPassword);
    }
}