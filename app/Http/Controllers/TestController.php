<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCharacteristic;

class TestController extends Controller
{
    public function index()
    {
        // Fetch the current user's characteristics if already answered
        $user = auth()->user();
        $characteristics = $user->characteristics ?? null;

        if ($characteristics && !$characteristics->lying) {
            return redirect('/dashboard');
        }
        else{
            $questions = [
                '1. Do you have a wide range of hobbies?',
                '2. Are you a talkative person?',
                '3. Do you consider yourself lively?',
                '4. Do you feel proud when others do something good and the people around you believe it was your own doing?',
                '5. Have you ever been greedy to gain extra benefits for yourself?',
                '6. Will you do anything if you claim you want to, no matter what difficulties you face?',
                '7. Do you usually enjoy yourself at gatherings?',
                '8. Have you ever blamed others for something you did wrong?',
                '9. Do you like meeting strangers?',
                '10. Have you ever taken advantage of others?',
                '11. Do you like to go out (travel) often if the conditions allow?',
                '12. Do you sometimes talk about things you know nothing about?',
                '13. Would you rather read some books than meet other people?',
                '14. Do you have many friends?',
                "15. As a child, did you immediately obey adults' orders without complaining?",
                '16. Are you a carefree person?',
                '17. Do you usually take the initiative when meeting new people?',
                '18. Do you rarely talk when you are with others?',
                '19. Do you sometimes brag?',
                '20. Can you bring life to a dull occasion?',
                '21. Do you like telling jokes and talking about interesting things?',
                '22. Were you ever rude to your parents when you were a child?',
                '23. Do you like to spend all day with others together?',
                '24. Do you always wash your hands before eating?',
                '25. Do you respond smoothly when people ask you questions?',
                '26. Have you ever cheated in a game or card game?',
                '27. Do you like stressful work?',
                '28. Have you ever taken advantage of others for your own benefit?',
                '29. Do you participate in too many things and surpass the time you have available??',
                "30. Would you pay someone less if you knew for sure that you wouldn't be discovered?",
                '31. Can you host a successful party?',
                '32. Do you insist on doing things according to your ideas?',
                '33. Are your words and actions always consistent?',
                '34. Have you ever been late for an appointment or work?',
                '35. Do you like to have a lot of excitement and joy around you?',
                '36. Do you sometimes put off until tomorrow what you should do today?',
                '37. Do others think you are energetic?',
                '38. Are you willing to admit if you make a mistake?',
                '39. Do you always throw peels or waste paper into the trash can in the park or on the road?',
                '40. Have you ever cursed someone casually?',
                '41. Are you a sociable person?',
                // ... add more questions
            ];

            return view('test.index', compact('characteristics', 'questions'));
        }
    }

    public function submit(Request $request)
    {
        $user = auth()->user();

        // Check if the user has already submitted
        if ($user->characteristics) {
            // If the user is detected lying, allow them to retake the test
            if ($user->characteristics->lying) {
                $user->characteristics->delete(); // Delete the previous characteristics
            } else {
                return redirect('/dashboard')->with('status', 'You have already submitted the test.');
            }
        }

        // Process the submitted test
        // Define scoring rules for each question
        $scoringRules = [
            'ei' => [
                'Yes' => [1, 3, 5, 7, 9, 11, 14, 16, 17, 20, 21, 23, 25, 27, 29, 31, 35, 37, 41],
                'No' => [13, 18],
            ],
    
            'lie' => [
                'Yes' => [6, 15, 24, 33, 38, 39],
                'No' => [2, 4, 8, 10, 12, 19, 22, 26, 28, 30, 32, 34, 36, 40],
            ],
        ];

        // Initialize scores
        $eiScore = 0;
        $lieScore = 0;

        // Iterate through questions and calculate scores
        foreach ($scoringRules as $trait => $rules) {
            foreach ($rules['Yes'] as $question) {
                ${$trait . 'Score'} += $request->input("q" . $question) === 'Yes' ? 1 : 0;
            }
            foreach ($rules['No'] as $question) {
                ${$trait . 'Score'} += $request->input("q" . $question) === 'No' ? 1 : 0;
            }
        }

        // Check lying condition
        $lying = ($lieScore >= 18);

        // Determine personality based on ei_scale
        $personality = 'ambivert';
        if ($eiScore > 15) {
            $personality = 'extrovert';
        } elseif ($eiScore < 8) {
            $personality = 'introvert';
        }

        // Save the characteristics in the database
        $characteristics = new UserCharacteristic([
            'ei_scale' => $eiScore,
            'lie_scale' => $lieScore,
            'lying' => $lying,
            'personality' => $personality,
        ]);

        $user->characteristics()->save($characteristics);

        if ($lying) {
            return redirect('/test')->with('warning', 'You have been identified as lying. Please retake the test.');
        }

        return redirect('/dashboard')->with('status', 'Test submitted successfully.');
    }
}
