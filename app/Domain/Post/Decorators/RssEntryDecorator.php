<?php

namespace Domain\Post\Decorators;

use Carbon\Carbon;
use ErrorException;
use Illuminate\Support\Collection;
use Zend\Feed\Reader\Entry\AbstractEntry;

class RssEntryDecorator extends AbstractEntry
{
    /** @var \Zend\Feed\Reader\Entry\AbstractEntry|\Zend\Feed\Reader\Entry\EntryInterface */
    private $decoratedEntry;

    /** @var \Illuminate\Support\Collection|\Domain\Post\Models\Tag[] */
    private $tags;

    /** @var \SimpleXMLElement */
    private $simpleDom;

    public function __construct(AbstractEntry $entry, Collection $tags)
    {
        $this->decoratedEntry = $entry;
        $this->tags = $tags;

        parent::__construct($entry->entry, $entry->entryKey, $entry->data['type']);

        $this->simpleDom = simplexml_import_dom($this->entry);
    }

    public function createdAt(): ?Carbon
    {
        $date = $this->data['datecreated']
            ?? $this->data['datemodified']
            ?? null;

        if (! $date && $this->entry->getElementsByTagName('pubDate')->length > 0) {
            $date = $this->entry->getElementsByTagName('pubDate')->item(0)->lastChild->textContent;
        }

        if (! $date && $this->entry->getElementsByTagName('updated')->length > 0) {
            $date = $this->entry->getElementsByTagName('updated')->item(0)->lastChild->textContent;
        }

        if (! $date) {
            $date = now();
        }

        return Carbon::make($date);
    }

    public function title(): string
    {
        $title = $this->decoratedEntry->getTitle();

        $title = preg_replace_callback("/(&#[0-9]+;)/", function ($match) {
            return mb_convert_encoding($match[1], "UTF-8", "HTML-ENTITIES");
        }, $title);

        return $title;
    }

    public function url(): string
    {
        return $this->decoratedEntry->getLink();
    }

    public function categories(): array
    {
        dd($this->simpleDom);
    }

    public function tags(): Collection
    {
        $searchContent = $this->title() . ' ' . $this->getContent();

        $foundTags = [];

        foreach ($this->tags as $tag) {
            $foundTags[$tag->id] = [];

            foreach ($tag->keywords as $keyword) {
                $matches = [];

                preg_match_all("/\b({$keyword})\b/i", $searchContent, $matches);

                $foundTags[$tag->id][$keyword] = count($matches[0]);

            }
        }

        $threshold = 2;

        $tagsByVotes = collect($foundTags)
            ->map(function (array $keywords) {
                return array_reduce($keywords, function (?int $sum, int $current) {
                    $sum = $sum ?? 0;

                    return $sum + $current;
                });
            })
            ->filter(function (int $count) use ($threshold) {
                return $count >= $threshold;
            })
            ->sortByDesc(function (int $count) {
                return $count;
            });

        return $tagsByVotes
            ->take(2)
            ->map(function (int $count, int $id) {
                return $id;
            });
    }

    public function getContent(): string
    {
        $contentTags = [
            'content:encoded',
            'content',
            'description',
            'summary',
        ];

        foreach ($contentTags as $contentTag) {
            try {
                $content = $this->simpleDom->xpath($contentTag);
            } catch (ErrorException $exception) {
                continue;
            }

            if (! $content) {
                $content = $this->simpleDom->{$contentTag} ?? null;
            }

            if (! $content) {
                continue;
            }

            return (string) $content[0];
        }

        return '';
    }
}
