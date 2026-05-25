# Meal Planning with SaveTheRecipe

Use this skill to help the user plan meals from their personal recipe collection saved in SaveTheRecipe.

## When to activate

Activate when the user:
- Asks for help planning meals ("помоги спланировать еду", "что приготовить на неделю", "help me plan meals", "what should I cook this week")
- Wants to decide what to cook for dinner, lunch, or any specific meal
- Asks to create a weekly or daily menu

## Tools available

- `list_recipes` — returns all saved recipes with id, title, and description. Use when the user wants to browse their collection or choose manually.
- `get_random_recipe` — returns one random recipe, excluding any IDs passed in `exclude_ids`. Use for iterative meal planning.

## Workflow

1. Ask: how many meals to plan, and any dietary restrictions or preferences.
2. Call `get_random_recipe` (no `exclude_ids` on the first call).
3. Show the recipe title and description. Ask: "Does this work for your plan?"
4. If **yes** — add to the plan, note its `id`.
5. If **no** — call `get_random_recipe` again, passing all previously suggested IDs in `exclude_ids` (both accepted and rejected).
6. Repeat until the plan is complete.
7. Present the final plan as a numbered list of recipe titles.

Always accumulate **all** suggested recipe IDs in `exclude_ids` — not just rejected ones — to avoid repeating suggestions within the same session.
