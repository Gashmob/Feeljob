<?php


namespace App;


use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class Utils
{
    /**
     * @param int $n
     * @return string
     * @throws Exception
     */
    private static function randomString(int $n): string
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
    public static function uploadImage(string $directory): string
    {
        if (isset($_FILES[$directory]) && $_FILES[$directory]['error'] === UPLOAD_ERR_OK) {
            // Infos sur le fichier téléchargé
            $fileTmpPath = $_FILES[$directory]['tmp_name'];
            $fileName = $_FILES[$directory]['name'];
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
    public static function passwordEncrypt(string $password): array
    {
        $salt = self::randomString(16);

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
    public static function passwordVerify(string $userPassword, string $userSalt, string $passwordToVerify): bool
    {
        return password_verify(hash('sha512', $passwordToVerify . $userSalt), $userPassword);
    }

    /**
     * @param MailerInterface $mailer
     * @param string $email
     * @param string $prenom
     * @param string $nom
     * @param int $id
     * @return int
     * @throws TransportExceptionInterface
     */
    public static function sendMailAndWait(MailerInterface $mailer, string $email, string $prenom, string $nom, int $id): int
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@fealjob.com')
            ->to($email)
            ->htmlTemplate('emails/verification.html.twig')
            ->context([
                'nom' => $nom,
                'prenom' => $prenom,
                'id' => $id
            ]);
        $mailer->send($email);

        return $id;
    }

    /**
     *
     * Author: semicolonworld
     * Function Name: getDistance()
     * $addressFrom => From address.
     * $addressTo => To address.
     * $unit => Unit type.
     *
     * @param string $addressFrom
     * @param string $addressTo
     * @param string $unit
     * @return float
     */
    public static function getDistance(string $addressFrom, string $addressTo, string $unit = 'K'): float
    {
        //Change address format
        $formattedAddrFrom = str_replace(' ','+',$addressFrom);
        $formattedAddrTo = str_replace(' ','+',$addressTo);

        //Send request and receive json data
        $geocodeFrom = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&sensor=false');
        $outputFrom = json_decode($geocodeFrom);
        $geocodeTo = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$formattedAddrTo.'&sensor=false');
        $outputTo = json_decode($geocodeTo);

        //Get latitude and longitude from geo data
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
        $latitudeTo = $outputTo->results[0]->geometry->location->lat;
        $longitudeTo = $outputTo->results[0]->geometry->location->lng;

        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}