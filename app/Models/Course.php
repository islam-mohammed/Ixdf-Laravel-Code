<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property Collection|Lesson[] $lessons
 * @property Collection|Quiz[] $quizzes
 */
final class Course extends Model
{
    use HasFactory;

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function quizzes(): HasManyThrough
    {
        return $this->hasManyThrough(Quiz::class, Lesson::class)->orderBy('created_at');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CourseScore::class);
    }

    public function enroll(Authenticatable $user): CourseEnrollment
    {
        return CourseEnrollment::create([
            'course_id' => $this->getKey(),
            'user_id' => $user->getAuthIdentifier(),
        ]);
    }

    /**
     * return global and local course statistics array
     *
     * @param  User  $user
     * @return array $statistics

     */
    public function getCourseStatistics(User $user): array
    {
        // get global group scores.
        $groupedGlobalScores = $this->scores()
            ->with('user')
            ->get()
            ->groupBy('score')
            ->sortKeysDesc();
        // generate global ranks for all users.
        $globalRankedScore = $this->generateRankedScore($groupedGlobalScores, $user->id);
        // generate the tiers.
        $globalTiers = $this->generateTiers($globalRankedScore, $user->id);

        // get local group scores.
        $groupedLocalScores = $this->scores()
            ->whereHas('user', function (Builder $query) use ($user) {
                $query->where('country_code', $user->country_code);
            })
            ->with('user')
            ->get()
            ->groupBy('score')
            ->sortKeysDesc();
        // generate local ranks for all users.
        $localRankedScore = $this->generateRankedScore($groupedLocalScores, $user->getAuthIdentifier());
        // generate the tiers.
        $localTiers = $this->generateTiers($localRankedScore, $user->getAuthIdentifier());

        $user->load('country');

        return [
            'globalTiers' => $globalTiers,
            'localTiers' => $localTiers,
            'user' => $user,
        ];
    }

    private function generateRankedScore(Collection $groupedScores, int $userId): Collection
    {
        // Create a new empty collection (used to extract users with ranks from scores group).
        $rankedScore = collect([]);
        // Set the rank to first
        $rank = 1;
        // Iterates over the group of scores
        foreach ($groupedScores as $groupScore) {
            // get the user index
            $userIndex = $groupScore->search(function ($item) use ($userId) {
                return $item->user_id === $userId;
            });
            if ($userIndex) {
                // this should push first the current user to the ranked score
                // to take precedence over the other users in the same score level

                // get the current user raw from the group score collection using the user index.
                $raw = $groupScore[$userIndex];
                // push the current user raw to the ranked score collection
                $rankedScore->push([
                    'rank' => $rank,
                    'user_id' => $raw['user_id'],
                    'name' =>  $raw['user']['name'],
                    'score' => $raw['score'],
                    'last_added_score' => $raw['last_added_score'],
                ]);
                // remove the current user raw from the ranked score
                $groupScore->splice($userIndex, 1);
            }
            // if there are any remaining users in the same score group then
            // add them to the ranked score collection
            foreach ($groupScore as $score) {
                $rankedScore->push([
                    'rank' => $rank,
                    'user_id' =>  $score['user_id'],
                    'name' =>  $score['user']['name'],
                    'score' => $score['score'],
                    'last_added_score' => $score['last_added_score'],
                ]);
            }
            // go to the next rank based on the next score group.
            $rank++;
        }
        return $rankedScore;
    }


    private function generateTiers($rankedScore, int $userId): array
    {
        $userIndex = $rankedScore->search(function ($score) use ($userId) {
            return $score['user_id'] === $userId;
        });
        $userRank = $rankedScore->where('user_id', $userId)->first()['rank'];
        $userPosition = $userIndex ? $userIndex + 1 : null;
        $numberOfRanks = count($rankedScore);

        $topTier = null;
        $middleTier = null;
        $bottomTier = null;

        // create the top tier
        $topTier = $rankedScore->take(3)->values();
        // create the bottom tier
        $bottomTier = $rankedScore->take(-3)->whereNotIn('user_id', $topTier->pluck('user_id'))->values();

        // create the middel tier
        if ($userPosition > 3 && $userPosition < $numberOfRanks - 3) {
            $middleTier[] = $rankedScore[$userIndex - 1];
            $middleTier[] = $rankedScore[$userIndex];
            $middleTier[] = $rankedScore[$userIndex + 1];
            $middleTier = collect($middleTier);
        }

        // should be atleast 2 tiers to check sequence
        if ($topTier->count() > 0 && $bottomTier->count() > 0) {
            $this->generateSequence($topTier, $bottomTier,  $middleTier);
        }

        return [
            'topTier' => $topTier,
            'middleTier' => $middleTier,
            'bottomTier' => $bottomTier,
            'userRank' => $userRank
        ];
    }


    private function generateSequence(&$topTier, &$bottomTier,  &$middleTier): void
    {
        $lastRankOnTop = $topTier->last()['rank'];
        $firstRankOnBottom = $bottomTier->first()['rank'];
        if (!$middleTier) {
            if ($lastRankOnTop === $firstRankOnBottom - 1) {
                $topTier = [...$topTier,...$bottomTier->splice(0)];
            }
        } else {
            $firstRankOnMid = $middleTier->first()['rank'];
            $lastRankOnMid = $middleTier->last()['rank'];

            if ($lastRankOnTop === $firstRankOnMid - 1) {

                $topTier = [...$topTier, ...$middleTier->splice(0)];
                // reevaluate the top the last rank in top after merging with the middle tier.
                $lastRankOnTop = collect($topTier)->last()['rank'];
                if ($lastRankOnTop === $firstRankOnBottom - 1) {
                    $topTier = [...$topTier,...$bottomTier->splice(0)];
                }
            } else if ($lastRankOnMid === $firstRankOnBottom - 1) {
                $middleTier = [...$middleTier,...$bottomTier->splice(0)];
            }
        }
    }
}
