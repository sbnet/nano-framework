<?php
/**
* The file cache
*
* @package NanoFramework\Utilities\Cache
*/
namespace NanoFramework\Utilities\Cache;

/**
* File Cache class
*
* Les fichiers ont une durée de vie (ttl) de une heure par défaut, réglable dans la propriété $ttl
* On peut désactiver le nettoyage automatique en mettant cette propriété à 0.
*
* De plus on peut aussi paramétrer la fréquence de nettoyage des fichiers trop vieux :
* on vide le cache une fois toutes les x requêtes de lecture seulement, cette fréquence est déterminée
* par la propriété $hits_limit et est fixée à 200 par défaut.
*
* @author Stephane BRUN
*/
class File implements iCacheDriver
{
    public $ttl = 3600; // 1 heure
    public $hits_limit = 200;
    
    private $dir;
       
    public function __construct($dir)
    {
        $this->set_dir($dir);
    }
       
    public function set_dir($dir)
    {
        $this->dir = $dir;
    }
    
    /**
     * Récupère une variable depuis le cache
     * et supprimer les fichiers cache trop vieux, pour désactiver
     * cette fonction il suffit de mettre $this->ttl à 0
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        $key = md5($name);
        $data = false;
        
        if(file_exists($this->dir.$key))
        {
            $serialized = file_get_contents($this->dir.$key);
            // Si on a fixé une durée de vie du cache, on supprime les fichiers trop vieux
            if($this->ttl > 0)
            {
                if(file_exists($this->dir.'hits_count'))
                {
                    $hits_count = file_get_contents($this->dir.'hits_count');
                    file_put_contents($this->dir.'hits_count', $hits_count+1);
                }

                $this->clear_old();
            }
            $data =  unserialize($serialized);
        }        
        return $data;
    }

    /**
     * Stocke une variable dans le cache
     *
     * @param string $name
     * @param mixed $data
     */
    public function store($name, $data)
    {
        $key = md5($name);
        $serialized = serialize($data);
        file_put_contents($this->dir.$key, $serialized);
    }

    /**
     * Supprime une variable du cache
     *
     * @param string $name
     * @return bool
     */
    public function delete($name)
    {
        $retour = false;

        if($this->check_for($name))
        {
            $retour = unlink($this->dir.md5($name));
        }

        return $retour;
    }

    /**
     * Vérifie si une variable est dans le cache
     *
     * @param string $name
     * @return bool
     */
    public function check_for($name)
    {
        $key = md5($name);
        
        $retour = false;
        if(file_exists($this->dir.$key))
        {
            $retour = true;
        }
        
        return $retour;
    }

    /**
     * Vide complètement le cache
     */
    public function clear_all()
    {
        $handle = opendir($this->dir);
        while($current_file = readdir($handle))
        {
            if(($current_file != '.') && ($current_file != '..'))
            {
                unlink($this->dir.$current_file);
            }
        }
        closedir($handle);
    }

    /**
     * Supprime les fichiers trop vieux
     */
    public function clear_old()
    {
        // Récupère le 'hit_count'
        if(file_exists($this->dir.'hits_count'))
        {
            $hits_count = file_get_contents($this->dir.'hits_count');
        }
        else
        {
            file_put_contents($this->dir.'hits_count', '0');
            $hits_count = 0;
        }

        // Vide le cache
        if($hits_count >= $this->hits_limit)
        {
            // Remet à zero le compteur de hits
            file_put_contents($this->dir.'hits_count', '0');

            $handle = opendir($this->dir);
            while($current_file = readdir($handle))
            {
                if(($current_file != '.') && ($current_file != '..'))
                {
                    $file = $this->dir.$current_file;

                    if((time()-filemtime($file)) > $this->ttl)
                    {
                        unlink($file);
                    }
                }
            }
            closedir($handle);
        }
    }
}
