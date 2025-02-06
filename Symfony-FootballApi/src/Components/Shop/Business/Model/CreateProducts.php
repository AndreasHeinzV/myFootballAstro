<?php

declare(strict_types=1);

namespace App\Components\Shop\Business\Model;

use App\Components\Api\ApiRequestFacade;
use App\Components\Shop\Persistence\Dtos\ProductDto;
use App\Components\Shop\Persistence\Mapper\ProductMapper;

readonly class CreateProducts
{
    public function __construct(
        private ApiRequestFacade $apiRequesterFacade,
        private ProductMapper $productMapper,
    ) {
    }

    public function createProducts(int $teamId): array
    {
        $teamDtoArray = $this->apiRequesterFacade->getTeam($teamId);

        if (empty($teamDtoArray)) {
            return [];
        }
        $squad = $teamDtoArray['squad'];
        if (empty($squad)) {
            return [];
        }
        $soccerImageLink = 'https://cdn.media.amplience.net/i/frasersdev/37966518_o?fmt=auto&upscale=false&w=345&h=345&sm=c&$h-ttl$';
        $teamName = $teamDtoArray['teamName'];
        $productsArray = [];

        foreach ($squad as $team) {
            $productsArray[] = $this->productMapper->createProductDto(
                'soccerJersey',
                $teamName,
                $team->name.' soccer jersey',
                $soccerImageLink,
                null,
                null,
                null
            );
        }
        $cupImage = 'https://t4.ftcdn.net/jpg/00/72/09/65/360_F_72096563_ei7KGRxgaKIX3GU2gFKWS9sxCrudCe4g.jpg';
        $scarfImage = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTg_Puj8YY6Yd4DIS230gL-k8IHVCG9T4QjZQ&s';
        $productsArray[] = $this->productMapper->createProductDto('cup', $teamName, 'cup', $cupImage, null, null, null);
        $productsArray[] = $this->productMapper->createProductDto(
            'scarf',
            $teamName,
            $teamName.' scarf',
            $scarfImage,
            null,
            null,
            null
        );

        return $productsArray;
    }

    public function createProduct(int $teamId, string $category, string $productName): ?ProductDto
    {
        $teamDtoArray = $this->apiRequesterFacade->getTeam($teamId);
        $squad = $teamDtoArray['squad'];
        $teamName = $teamDtoArray['teamName'];
        $productDto = null;
        foreach ($squad as $team) {
            if (str_contains($productName, $team->name)) {
                $productDto = $this->productMapper->createProductDto(
                    'soccerJersey',
                    $teamName,
                    $team->name.' '.$category,
                    $this->getImageLink($category),
                    null,
                    null,
                    null
                );
            }
        }

        return $productDto;
    }

    private function getImageLink(string $category): ?string
    {
        $cupImage = 'https://t4.ftcdn.net/jpg/00/72/09/65/360_F_72096563_ei7KGRxgaKIX3GU2gFKWS9sxCrudCe4g.jpg';
        $scarfImage = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTg_Puj8YY6Yd4DIS230gL-k8IHVCG9T4QjZQ&s';
        $soccerImageLink = 'https://cdn.media.amplience.net/i/frasersdev/37966518_o?fmt=auto&upscale=false&w=345&h=345&sm=c&$h-ttl$';
        if ('soccerJersey' === $category) {
            return $soccerImageLink;
        }
        if ('scarf' === $category) {
            return $scarfImage;
        }
        if ('cup' === $category) {
            return $cupImage;
        }

        return null;
    }
}
