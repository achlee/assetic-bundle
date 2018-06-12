<?php

namespace CourseHero\UtilsBundle\Assetic;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\AssetReference;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;

/**
 * Adds cache busting based on the hash of all asset contents
 *
 *
 * TODO: Move this to open source package
 * @package CourseHero\AsseticFilehashBuster
 * @author Jason Wentworth <wentwj@gmail.com>
 */
class FilehashCacheBustingWorker extends CacheBustingWorker
{
    public function process(AssetInterface $asset, AssetFactory $factory)
    {
        if (!($asset instanceof AssetCollectionInterface)) {
            return;
        }

        return parent::process($asset, $factory);
    }

    protected function getHash(AssetInterface $assetCollection, AssetFactory $factory): string
    {
        $hash = hash_init('sha1');
        $content = $this->getUnfilteredAssetContent($assetCollection);
        hash_update($hash, $content);
        foreach ($assetCollection as $asset) {
            hash_update($hash, serialize($asset->getFilters()));
        }
        return substr(hash_final($hash), 0, 7);
    }

    protected function getUnfilteredAssetContent(AssetCollectionInterface $assetCollection): string
    {
        $cloned = clone $assetCollection;
        $cloned->clearFilters();
        foreach ($cloned as $asset) {
            $asset->clearFilters();
        }
        $cloned->load();
        return $cloned->getContent();
    }
}
