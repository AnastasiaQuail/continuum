<?php

declare(strict_types=1);

namespace Continuum\Twig;

use Twig\Attribute\AsTwigFunction;

final readonly class BirdNameFunction
{
    private const array NAMES = [
        'Eurasian Blackbird',
        'House Sparrow',
        'Common Starling',
        'Common Chaffinch',
        'Eurasian Blue Tit',
        'Great Tit',
        'Eurasian Nuthatch',
        'Eurasian Jay',
        'Eurasian Magpie',
        'Hooded Crow',
        'Carrion Crow',
        'Common Wood Pigeon',
        'Stock Dove',
        'Ring-necked Parakeet',
        'Rock Pigeon',
        'House Martin',
        'Barn Swallow',
        'Common Swift',
        'Eurasian Wren',
        'Common Nightingale',
        'European Robin',
        'Meadow Pipit',
        'Eurasian Tree Sparrow',
        'Common Chiffchaff',
        'Willow Warbler',
        'Blackcap',
        'Garden Warbler',
        'Eurasian Blackcap',
        'Great Reed Warbler',
        'Sedge Warbler',
        'Reed Warbler',
        'Eurasian Skylark',
        'Northern Lapwing',
        'Eurasian Oystercatcher',
        'Common Redshank',
        'Black-tailed Godwit',
        'Ruff',
        'Common Snipe',
        'Eurasian Curlew',
        'Dunlin',
        'Common Sandpiper',
        'Eurasian Whimbrel',
        'Eurasian Sparrowhawk',
        'Common Buzzard',
        'Northern Goshawk',
        'Common Kestrel',
        'Peregrine Falcon',
        'Eurasian Collared Dove',
        'Little Owl',
        'Barn Owl',
        'Tawny Owl',
        'Great Spotted Woodpecker',
        'Eurasian Green Woodpecker',
        'Lesser Spotted Woodpecker',
        'European Goldfinch',
        'Common Linnet',
        'Eurasian Bullfinch',
        'Common Starling',
        'Eurasian Treecreeper',
        'Eurasian Jay',
        'Coal Tit',
        'Long-tailed Tit',
        'Marsh Tit',
        'Eurasian Blackcap',
        'Bearded Reedling',
        'Common Crane',
        'Whooper Swan',
        'Mute Swan',
        'Greylag Goose',
        'Barnacle Goose',
        'Canada Goose',
        'Egyptian Goose',
        'Common Teal',
        'Mallard',
        'Eurasian Wigeon',
        'Northern Shoveler',
        'Common Pochard',
        'Tufted Duck',
        'Common Goldeneye',
        'Eurasian Coot',
        'Common Moorhen',
        'Great Cormorant',
        'Grey Heron',
        'Little Egret',
        'Great Egret',
        'Black-headed Gull',
        'European Herring Gull',
        'Lesser Black-backed Gull',
        'Common Tern',
        'Sandwich Tern',
        'Eurasian Hobby',
        'Common Kingfisher',
        'Eurasian Tree Sparrow',
        'Eurasian Jay',
        'Eurasian Siskin',
        'Redpoll',
        'European Greenfinch',
        'Yellowhammer',
        'Corn Bunting',
        'Skylark',
    ];

    public function __construct(
        private UserDateFunction $userDateFunction,
    ) {}

    #[AsTwigFunction('bird_name')]
    public function __invoke(): string
    {
        $currentDate = ($this->userDateFunction)();

        /** @var non-negative-int $dayOfYear */
        $dayOfYear = (int) $currentDate->format('z');

        return self::NAMES[$dayOfYear % count(self::NAMES)];
    }
}
