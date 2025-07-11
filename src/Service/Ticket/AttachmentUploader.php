<?php

/**
 * File: AttachmentUploader.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Ticket;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class AttachmentUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
        private LoggerInterface $logger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $fileOriginalName = $file->getClientOriginalName();
        $safeFilename = $this->slugger->slug($fileOriginalName);
        $fileName = $safeFilename.'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw new FileException($e->getMessage());
        }

        return $fileName;
    }

    private function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
