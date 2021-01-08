<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;

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

    public function getGlobalScore()
    {
        $sScores = [];
        $scores = $this->scores()
            ->with('user')
            ->get()
            ->groupBy('score')
            ->sortKeysDesc();

        $rank = 1;
        foreach ($scores as $key => $score) {
            $scoreArray = $score->toArray();
            $userIndex = $score->search(function ($item) {
                return $item->user_id === 106;
            });
            if ($userIndex) {

                $row = $score[$userIndex];

                array_push($sScores, [
                    'rank' => $rank,
                    'user_id' =>  $row->user_id,
                    'name' =>  $row->user->name,
                    'score' => $row->score,
                    'last_added_score' => $row->last_added_score,
                ]);

                array_splice($scoreArray, $userIndex);
            }

            $rank++;
        }
        return $scores;

        // if (count($scores)) {
        //     $topScore = $scores->take(6);

        // }
        // return $topScore;
        // // abort(404, "There are no score for course {$title}");




        // // return;
        // ->quizzes()
        // ->with(['answers' => function ($query) {
        //     return $query->with(['user' => function ($query) {
        //         return $query->select('id', 'name');
        //     }]);
        // }])
        // ->get()
        // ->pluck('answers')
        // ->flatten(1)
        // ->map(function ($answer) {
        //     return ['id' => $answer->user->id, 'name' => $answer->user->name, 'score' => $answer->score];
        // })
        // ->each(function ($entity) use (&$scores) {
        //     if (array_key_exists($entity['id'], $scores)) {
        //         $scores[$entity['id']]['score'] += $entity['score'];
        //     } else {
        //         $scores[$entity['id']] = [
        //             "id" => $entity['id'],
        //             "name" => $entity['name'],
        //             "score" => $entity['score'],
        //         ];
        //     }
        // });

        // check if user in first 6

        //chek if the user in the first 3

        // check if user in last 6

        //




        // return collect($scores)->sortBy(['score', 'desc'])->take(-3);
    }
}
