<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 14:20 08/02/14
 */

namespace Walrus\core\objects;

use R;
use SessionHandlerInterface;

/**
 * Class SessionHandler
 * @package Walrus\core\objects
 */
class SessionHandler implements SessionHandlerInterface
{
    /**
     * @param $savePath
     * @param $sessionName
     *
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        //initialisation du gestionnaire de sessions
        return true;
    }

    /**
     * @return bool
     */
    public function close()
    {
        //fermeture / destruction du gestionnaire de sessions
        return true;
    }

    /**
     * @param $session_id
     *
     * @return bool
     */
    public function read($session_id)
    {
        $sessionData = R::findOne(
            'session_data',
            'session_id = :session_id',
            array(':session_id' => $session_id)
        );

        if (empty($data)) {
            return false;
        } else {
            return $sessionData;
        }
    }

    /**
     * @param $session_id
     * @param $data
     *
     * @return mixed
     */
    public function write($session_id, $data)
    {
        $expire = intval(time() + 7200);//calcul de l'expiration de la session (ici par exemple, deux heures).

        $session = R::findOne(
            'sessions',
            'session_id = :session_id',
            array(':session_id' => $session_id)
        );

        if (!$session) {
            $sessions = R::dispense('sessions');
            $sessions->session_id = $session_id;
            $sessions->session_expire = $expire;
            $sessions->session_data = $data;

            R::store($sessions);
        } else {
            $sessions = R::load('sessions', $session->id);
            $sessions->session_expire = $expire;
            $sessions->session_data = $data;

            R::store($sessions);
        }

        return true;
    }

    /**
     * @param $session_id
     *
     * @return mixed
     */
    public function destroy($session_id)
    {
        $sessions = R::load('sessions', $session_id);
        R::trash($sessions);

        return true;
    }

    /**
     * @param $maxlifetime
     *
     * @return mixed
     */
    public function gc($maxlifetime)
    {
        $sessions = R::find(
            'sessions',
            'WHERE session_expire < :time',
            array(':time' => time())
        );

        R::trashAll($sessions);

        return true;
    }
}
