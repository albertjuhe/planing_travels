<?php

namespace App\Domain\Journal\Model;

use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

class JournalEntry
{
    public const MOOD_HAPPY = 'happy';
    public const MOOD_EXCITED = 'excited';
    public const MOOD_RELAXED = 'relaxed';
    public const MOOD_TIRED = 'tired';
    public const MOOD_ADVENTUROUS = 'adventurous';

    public const MOODS = [
        self::MOOD_HAPPY => '😊',
        self::MOOD_EXCITED => '🤩',
        self::MOOD_RELAXED => '😌',
        self::MOOD_TIRED => '😴',
        self::MOOD_ADVENTUROUS => '🧗',
    ];

    /** @var string */
    private $id;

    /** @var Travel */
    private $travel;

    /** @var User */
    private $author;

    /** @var \DateTime */
    private $entryDate;

    /** @var string|null */
    private $title;

    /** @var string */
    private $content;

    /** @var string|null */
    private $mood;

    /** @var string|null JSON: {tempMin, tempMax, code, icon, description} */
    private $weatherSnapshot;

    /** @var bool */
    private $isPublic = false;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    /** @var Collection|JournalPhoto[] */
    private $photos;

    public function __construct(Travel $travel, User $author, \DateTime $entryDate, string $content)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->travel = $travel;
        $this->author = $author;
        $this->entryDate = $entryDate;
        $this->content = $content;
        $this->isPublic = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->photos = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTravel(): Travel
    {
        return $this->travel;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getEntryDate(): \DateTime
    {
        return $this->entryDate;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
        $this->updatedAt = new \DateTime();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
        $this->updatedAt = new \DateTime();
    }

    public function getMood(): ?string
    {
        return $this->mood;
    }

    public function setMood(?string $mood): void
    {
        if ($mood !== null && !array_key_exists($mood, self::MOODS)) {
            throw new \InvalidArgumentException("Invalid mood: {$mood}");
        }
        $this->mood = $mood;
        $this->updatedAt = new \DateTime();
    }

    public function getMoodEmoji(): ?string
    {
        return $this->mood ? (self::MOODS[$this->mood] ?? null) : null;
    }

    public function getWeatherSnapshot(): ?string
    {
        return $this->weatherSnapshot;
    }

    public function setWeatherSnapshot(?string $weatherSnapshot): void
    {
        $this->weatherSnapshot = $weatherSnapshot;
    }

    public function getWeatherSnapshotArray(): ?array
    {
        if ($this->weatherSnapshot === null) {
            return null;
        }

        return json_decode($this->weatherSnapshot, true) ?: null;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
        $this->updatedAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(JournalPhoto $photo): void
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
        }
    }

    public function removePhoto(JournalPhoto $photo): void
    {
        $this->photos->removeElement($photo);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'entryDate' => $this->entryDate->format('Y-m-d'),
            'title' => $this->title,
            'content' => $this->content,
            'mood' => $this->mood,
            'moodEmoji' => $this->getMoodEmoji(),
            'weatherSnapshot' => $this->getWeatherSnapshotArray(),
            'isPublic' => $this->isPublic,
            'authorUsername' => $this->author->getUsername(),
            'createdAt' => $this->createdAt->format('Y-m-d H:i'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i'),
            'photos' => $this->photos->map(fn ($p) => $p->toArray())->toArray(),
        ];
    }
}
