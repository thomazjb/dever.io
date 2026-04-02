<?php
/**
 * MinioComponent - Componente para integração com MinIO (S3 compatible)
 *
 * Fornece métodos para upload, download e deleção de arquivos
 * no bucket MinIO configurado via variáveis de ambiente Docker.
 *
 * @package app\components
 */

namespace app\components;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use yii\base\Component;
use Yii;

class MinioComponent extends Component
{
    /** @var S3Client Cliente S3 */
    private $_client;

    /**
     * Inicializa o cliente S3 apontando para o MinIO.
     */
    public function init()
    {
        parent::init();

        $this->_client = new S3Client([
            'version' => 'latest',
            'region' => getenv('MINIO_REGION') ?: 'us-east-1',
            'endpoint' => getenv('MINIO_ENDPOINT') ?: 'http://minio:9000',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => getenv('MINIO_KEY') ?: 'minio_access_key',
                'secret' => getenv('MINIO_SECRET') ?: 'minio_secret_key',
            ],
        ]);
    }

    /**
     * Faz upload de um arquivo para o MinIO.
     *
     * @param string $filePath Caminho local do arquivo
     * @param string $key Caminho/nome no bucket (ex: "tasks/123/arquivo.pdf")
     * @param string $mimeType Tipo MIME do arquivo
     * @return string URL pública do arquivo
     * @throws \Exception em caso de falha no upload
     */
    public function upload(string $filePath, string $key, string $mimeType): string
    {
        $bucket = getenv('MINIO_BUCKET') ?: 'dever-uploads';

        try {
            $result = $this->_client->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
                'ContentType' => $mimeType,
            ]);

            return $result['ObjectURL'] ?? $this->getUrl($key);
        } catch (AwsException $e) {
            Yii::error("Erro ao fazer upload para MinIO: " . $e->getMessage(), __METHOD__);
            throw new \Exception('Falha ao fazer upload do arquivo: ' . $e->getMessage());
        }
    }

    /**
     * Gera URL pré-assinada para acesso ao arquivo.
     *
     * @param string $key Caminho/nome no bucket
     * @param int $expiry Tempo de expiração em minutos (padrão: 60)
     * @return string URL pré-assinada
     */
    public function getPresignedUrl(string $key, int $expiry = 60): string
    {
        $bucket = getenv('MINIO_BUCKET') ?: 'dever-uploads';

        $cmd = $this->_client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);

        $request = $this->_client->createPresignedRequest($cmd, "+{$expiry} minutes");

        // Substituir endpoint interno pelo público para acesso via browser
        $url = (string) $request->getUri();
        $internalEndpoint = getenv('MINIO_ENDPOINT') ?: 'http://minio:9000';
        $publicEndpoint = getenv('MINIO_PUBLIC_ENDPOINT') ?: 'http://localhost:9000';

        return str_replace($internalEndpoint, $publicEndpoint, $url);
    }

    /**
     * Retorna URL pública do arquivo.
     *
     * @param string $key Caminho/nome no bucket
     * @return string URL pública
     */
    public function getUrl(string $key): string
    {
        $bucket = getenv('MINIO_BUCKET') ?: 'dever-uploads';
        $publicEndpoint = getenv('MINIO_PUBLIC_ENDPOINT') ?: 'http://localhost:9000';

        return "{$publicEndpoint}/{$bucket}/{$key}";
    }

    /**
     * Remove um arquivo do MinIO.
     *
     * @param string $key Caminho/nome no bucket
     * @return bool Sucesso da operação
     */
    public function delete(string $key): bool
    {
        $bucket = getenv('MINIO_BUCKET') ?: 'dever-uploads';

        try {
            $this->_client->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            Yii::error("Erro ao deletar do MinIO: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Verifica se um arquivo existe no bucket.
     *
     * @param string $key Caminho/nome no bucket
     * @return bool
     */
    public function exists(string $key): bool
    {
        $bucket = getenv('MINIO_BUCKET') ?: 'dever-uploads';

        try {
            $this->_client->headObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }
}
