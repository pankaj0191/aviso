<?php

namespace App\Nova;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class CategoryFile extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\CategoryFile';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Sub Category', 'subCategory', 'App\Nova\SubCategory'),

            Image::make('Image')->disk('spaces')
                ->path('/design-template/image')
                ->rules('mimes:jpeg,bmp,png,gif,svg,pdf', 'max:10240'),

            $this->fileField($request),

            Text::make('Original File Name', 'name')->onlyOnDetail(),

            Text::make('File Size', 'size', function ($value) {
                return number_format($value / (1000 * 1000), 2) . 'mb';
            })->onlyOnDetail(),

        ];
    }

    public function fileField($request)
    {
        return File::make('File')
            ->disk('spaces')
            ->path('/design-template/psd')
            ->storeOriginalName('name')
            ->storeSize('size')
            ->rules('max:204800')
            ->prunable();
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
