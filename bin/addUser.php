<?php
//user: c8f58c679d7be0b31f091917bff03369372142111dff5f2e17c45bb44e77b58d # equals to changePassw0rd
require __DIR__ . '/../vendor/autoload.php';
$sphring = new \Arthurh\Sphring\Sphring(__DIR__ . '/../sphring/bin.yml');
$sphring->setRootProject(__DIR__ . '/..');
$sphring->setComposerLockFile(__DIR__ . '/../composer.lock');

$sphring->loadContext();

$encoder = $sphring->getBean('encoder');
if (!($encoder instanceof \Sphring\MicroWebFramework\Security\Encoder)) {
    echo "Invalid encoder\n";
    flush();
    exit(1);
}
if (empty($argc) || $argc < 3) {
    echo "Need two arguments: <user_name> <password>\n";
    flush();
    exit(1);
}
$userFile = __DIR__ . '/../config/users.yml';

$users = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($userFile));
if (isset($users['user'])) {
    unset($users['user']);
}
$users[$argv[1]] = $encoder->crypt($argv[2]);
file_put_contents($userFile, \Symfony\Component\Yaml\Yaml::dump($users));
echo "User " . $argv[1] . " created!\n";
flush();
exit(0);