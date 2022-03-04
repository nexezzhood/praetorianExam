<?php

namespace App\Commands;

use App\Services\Cache\Cache;
use App\Services\Cache\CacheInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/**
 * @property CacheInterface $redis
 */
#[AsCommand(
    name: 'app:generate-pair-keys',
)]
class PairGenerator extends Command
{
    protected function configure()
    {
        $this->setName('app:generate-pair-keys')
            ->setDescription('Creates a pair of keys, public and private key.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redis = new Cache();
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
        $mailer = new Mailer($transport);

        $emailList = $redis->get('emailList');
        $email = array_pop($emailList);
        $emailCrc = crc32($email);

        $private_key = openssl_pkey_new();
        $public_key_pem = openssl_pkey_get_details($private_key)['key'];

        // store public key to redis
        $redis->set('publicKey_' . $email, $public_key_pem);

        openssl_pkey_export_to_file($private_key, 'storage/private_key_' . $emailCrc . '.key');

        //send email
        $emailToSend = (new Email())
            ->from('hello@example.com')
            ->to($email)
            ->subject('Private Key')
            ->text('Private Key')
            ->attachFromPath('storage/private_key_' . $emailCrc . '.key')
            ->html('<p> Hello dear ' . $email . '</p> <br> <p>In the attachment section you can find your private key.</p>');

        $mailer->send($emailToSend);

        return Command::SUCCESS;
    }
}