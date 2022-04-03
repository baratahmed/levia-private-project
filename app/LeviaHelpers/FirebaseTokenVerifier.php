<?php
namespace App\LeviaHelpers;

class FirebaseTokenVerifier {

  private $keys_file = "firebase_securetokens.json";
  private $cache_file = "firebase_pkeys.cache";
  private $fbProjectId = null;
  private $debugMode = false;

  public function __construct($debugMode = false)
  {
    $this->fbProjectId = env('FIREBASE_PROJECT_ID');
    $this->keys_file = base_path() . "/storage/app/" . $this->keys_file;
    $this->cache_file = base_path() . "/storage/app/" . $this->cache_file;

    $this->debugMode = $debugMode;
  }

  public function verify_firebase_token($token = '', $uid = null, $phone_number = null)
  {
      $fbProjectId = $this->fbProjectId;
      $return = array();
      $return["success"] = false; // We'll make it true when conditions are matched
      $userId = $deviceId = "";
      $this->checkKeys();
      $pkeys_raw = $this->getKeys();
      if (!empty($pkeys_raw)) {
          $pkeys = json_decode($pkeys_raw, true);

          try {
              $decoded = \Firebase\JWT\JWT::decode($token, $pkeys, ["RS256"]);
              if ($this->debugMode) {
                  echo "<hr>BOTTOM LINE - the decoded data<br>";
                  print_r($decoded);
                  echo "<hr>";
              }
              if (!empty($decoded)) {
                  // do all the verifications Firebase says to do as per https://firebase.google.com/docs/auth/admin/verify-id-tokens
                  // exp must be in the future
                  $exp = $decoded->exp > time();
                  // ist must be in the past
                  $iat = $decoded->iat < time();
                  // aud must be your Firebase project ID
                  $aud = $decoded->aud == $fbProjectId;
                  // iss must be "https://securetoken.google.com/<projectId>"
                  $iss = $decoded->iss == "https://securetoken.google.com/$fbProjectId";
                  // sub must be non-empty and is the UID of the user or device
                  $sub = $decoded->sub;

                  $uidAndPhoneOkay = true;

                  // Does the UID match with IdToken
                  if (null !== $uid){
                    if ($sub !== $uid){
                      $return["error"] = "UID doesn't match with the one found from IdToken";
                      $uidAndPhoneOkay = false;
                    }
                  }

                  // Does the Phone Number match with IdToken
                  if (null !== $phone_number){
                    if ($decoded->phone_number !== $phone_number){
                      $return["error"] = "Phone Number doesn't match with the one found from IdToken";
                      $uidAndPhoneOkay = false;
                    }
                  }


                  if ($exp && $iat && $aud && $iss && !empty($sub) && $uidAndPhoneOkay) {
                      // we have a confirmed Firebase user!
                      // build an array with data we need for further processing
                      $return['success'] = true;
                      $return['UID'] = $sub;
                      $return['phone_number'] = $decoded->phone_number;
                  } else {
                      // $return["error"] = "Token is invalid. We couldn't match all the conditions.";
                      if ($this->debugMode) {
                          echo "NOT ALL THE THINGS WERE TRUE!<br>";
                          echo "exp is $exp<br>ist is $iat<br>aud is $aud<br>iss is $iss<br>sub is $sub<br>";
                      }
                  }
              }
          } catch (\UnexpectedValueException $unexpectedValueException) {
              $return['error'] = $unexpectedValueException->getMessage();
              if ($this->debugMode) {
                  echo "<hr>ERROR! " . $unexpectedValueException->getMessage() . "<hr>";
              }
          }
      }
      return $return;
  }
  /**
  * Checks whether new keys should be downloaded, and retrieves them, if needed.
  */
  public function checkKeys()
  {
      $cache_file = $this->cache_file;
      if (file_exists($cache_file)) {
          if ($this->debugMode) {
              echo "ARIF: Cache File Exists" . PHP_EOL;
          }
          $fp = fopen($cache_file, "r+");
          if (flock($fp, LOCK_SH)) {
              $contents = fread($fp, filesize($cache_file));
              if ($contents > time()) {
                  flock($fp, LOCK_UN);
              } elseif (flock($fp, LOCK_EX)) { // upgrading the lock to exclusive (write)
                  // here we need to revalidate since another process could've got to the LOCK_EX part before this
                  if (fread($fp, filesize($cache_file)) <= time())
                  {
                      $this->refreshKeys($fp);
                  }
                  flock($fp, LOCK_UN);
              } else {
                  throw new \RuntimeException('Cannot refresh keys: file lock upgrade error.');
              }
          } else {
              // you need to handle this by signaling error
          throw new \RuntimeException('Cannot refresh keys: file lock error.');
          }
          fclose($fp);
      } else {
          if ($this->debugMode) {
              echo "ARIF: Cache File Doesn't Exist" . PHP_EOL;
          }
          $this->refreshKeys();
      }
  }

  /**
   * Downloads the public keys and writes them in a file. This also sets the new cache revalidation time.
   * @param null $fp the file pointer of the cache time file
   */
  public function refreshKeys($fp = null)
  {
      $keys_file = $this->keys_file;
      $cache_file = $this->cache_file;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      $data = curl_exec($ch);
      $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      $headers = trim(substr($data, 0, $header_size));
      $raw_keys = trim(substr($data, $header_size));
      if ($this->debugMode) {
          echo "ARIF: " . $raw_keys . PHP_EOL;
      }
      if (preg_match('/age:[ ]+?(\d+)/i', $headers, $age_matches) === 1)
      {
          $age = $age_matches[1];
          if (preg_match('/cache-control:.+?max-age=(\d+)/i', $headers, $max_age_matches) === 1) {
              $valid_for = $max_age_matches[1] - $age;
              $fp = fopen($cache_file, "w");
              ftruncate($fp, 0);
              fwrite($fp, "" . (time() + $valid_for));
              fflush($fp);
              // $fp will be closed outside, we don't have to
              if ($this->debugMode) {
                  echo "ARIF: $keys_file File is being written" . PHP_EOL;
              }
              $fp_keys = fopen($keys_file, "w");
              if (flock($fp_keys, LOCK_EX)) {
                  fwrite($fp_keys, $raw_keys);
                  fflush($fp_keys);
                  flock($fp_keys, LOCK_UN);
              }
              fclose($fp_keys);
          }
      }
  }

  /**
   * Retrieves the downloaded keys.
   * This should be called anytime you need the keys (i.e. for decoding / verification).
   * @return null|string
   */
  public function getKeys()
  {
    $keys_file = $this->keys_file;
      $fp = fopen($keys_file, "r");
      $keys = null;
      if (flock($fp, LOCK_SH)) {
          $keys = fread($fp, filesize($keys_file));
          flock($fp, LOCK_UN);
      }
      fclose($fp);
      return $keys;
  }

}
