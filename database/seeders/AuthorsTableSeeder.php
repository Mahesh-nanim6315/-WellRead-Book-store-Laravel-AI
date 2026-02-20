<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            [
                'name' => 'J.K. Rowling',
                'bio' => 'Joanne Rowling, better known by her pen name J.K. Rowling, is a British author and philanthropist. 
                        She is best known for writing the Harry Potter fantasy series, which has won multiple awards and sold more than 500 million copies worldwide. 
                        Born in Yate, Gloucestershire, Rowling was working as a researcher and bilingual secretary for Amnesty International when she conceived the idea for the Harry Potter series. 
                        The seven-year period that followed saw the death of her mother, the birth of her first child, divorce from her first husband, and relative poverty until the first novel was published in 1997. 
                        Rowling has lived a "rags to riches" life story, in which she progressed from living on state benefits to being the world\'s first billionaire author. 
                        She has since lost her billionaire status due to giving away much of her earnings to charity, but remains one of the wealthiest writers in the world. 
                        Rowling is also known for her philanthropic efforts, having founded the charitable trust Volant, and supporting multiple charities including Lumos.',
                'image' => 'https://images.unsplash.com/photo-1551843073-4a9a5b6fcd5f?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Stephen King',
                'bio' => 'Stephen Edwin King is an American author of horror, supernatural fiction, suspense, crime, science-fiction, and fantasy novels. 
                His books have sold more than 350 million copies worldwide, and many have been adapted into films, television series, miniseries, and comic books. 
                King has published 64 novels, including seven under the pen name Richard Bachman, and five non-fiction books. 
                He has written approximately 200 short stories, most of which have been published in book collections. 
                King has received Bram Stoker Awards, World Fantasy Awards, and British Fantasy Society Awards for his works. 
                In 2003, the National Book Foundation awarded him the Medal for Distinguished Contribution to American Letters. 
                He has also received awards for his contribution to literature for his entire body of work, such as the 2004 World Fantasy Award for Life Achievement. 
                Many of King\'s stories are set in his home state of Maine, and he is known for his vivid character development and ability to tap into cultural fears.',
                'image' => 'https://images.unsplash.com/photo-1544725176-7c40e5a71c5e?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Toni Morrison',
                'bio' => 'Chloe Anthony Wofford Morrison, known as Toni Morrison, was an American novelist, essayist, book editor, and college professor. 
                Her first novel, The Bluest Eye, was published in 1970. The critically acclaimed Song of Solomon brought her national attention and won the National Book Critics Circle Award. 
                In 1988, Morrison won the Pulitzer Prize for Beloved. She gained worldwide recognition when she was awarded the Nobel Prize in Literature in 1993. 
                Morrison\'s works are praised for addressing the harsh consequences of racism in the United States and the Black American experience. 
                Her use of fantasy, her sinuous poetic style, and her rich interweaving of the mythic gave her stories great strength and texture. 
                She was honored with the National Humanities Medal in 2000, and received the Presidential Medal of Freedom in 2012. 
                Morrison served as professor emeritus at Princeton University and left an indelible mark on American literature.',
                'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'George R.R. Martin',
                'bio' => 'George Raymond Richard Martin, often referred to as GRRM, is an American novelist and short story writer in the fantasy, horror, and science fiction genres. 
                He is best known for his series of epic fantasy novels, A Song of Ice and Fire, which was adapted into the HBO series Game of Thrones. 
                Martin served as a story editor, producer, and scriptwriter for the television series The Twilight Zone and Beauty and the Beast. 
                He has won numerous awards including Hugo, Nebula, Locus, and World Fantasy Awards for his various works. 
                Martin\'s writing is characterized by complex characters, intricate plots, and a willingness to kill off major characters unexpectedly. 
                He is known for his "gardener" approach to writing, where stories evolve organically rather than being strictly planned in advance. 
                His works often explore themes of power, honor, betrayal, and the human condition in fantastical settings. 
                Martin continues to work on The Winds of Winter, the long-awaited sixth novel in A Song of Ice and Fire series.',
                'image' => 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Margaret Atwood',
                'bio' => 'Margaret Eleanor Atwood is a Canadian poet, novelist, literary critic, essayist, teacher, environmental activist, and inventor. 
                She has published eighteen books of poetry, eighteen novels, eleven books of non-fiction, nine collections of short fiction, and numerous other works. 
                Atwood is best known for her novels, including The Handmaid\'s Tale and its sequel The Testaments, which won the Booker Prize in 2019. 
                Her works encompass a variety of themes including gender and identity, religion and myth, climate change, and power politics. 
                Atwood\'s writing is noted for its psychological depth, feminist themes, and dystopian elements. 
                She has received numerous awards and honors including the Arthur C. Clarke Award, Prince of Asturias Award, and the National Book Critics Circle Award. 
                Atwood is a founder of the Writers\' Trust of Canada and a founding trustee of the Griffin Poetry Prize. 
                She remains one of the most influential and widely read contemporary authors in the English-speaking world.',
                'image' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Haruki Murakami',
                'bio' => 'Haruki Murakami is a Japanese writer. His books and stories have been bestsellers in Japan and internationally, with his work being translated into 50 languages. 
                His fiction, still criticized by Japan\'s literary establishment as un-Japanese, was influenced by Western writers from Chandler to Vonnegut by way of Brautigan. 
                Murakami\'s most notable works include Norwegian Wood, The Wind-Up Bird Chronicle, Kafka on the Shore, and 1Q84. 
                He has received numerous awards, including the World Fantasy Award, the Frank O\'Connor International Short Story Award, and the Jerusalem Prize. 
                Murakami\'s writing is characterized by surreal elements, melancholic themes, and a blending of fantasy with everyday life. 
                He is also known for his love of music, particularly jazz and classical, which often features prominently in his works. 
                Murakami\'s protagonists are often ordinary people who find themselves in extraordinary, dreamlike situations. 
                His unique style has earned him a dedicated international following and critical acclaim worldwide.',
                'image' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Chimamanda Ngozi Adichie',
                'bio' => 'Chimamanda Ngozi Adichie is a Nigerian writer whose works include novels, short stories, and nonfiction. 
                She was described in The Times Literary Supplement as "the most prominent" of a "procession of critically acclaimed young anglophone authors" from Nigeria. 
                Adichie\'s novels include Purple Hibiscus, Half of a Yellow Sun, and Americanah. Her short story collection, The Thing Around Your Neck, was published in 2009. 
                Her 2012 TED Talk "We Should All Be Feminists" started a worldwide conversation about feminism and was published as a book in 2014. 
                Adichie has received numerous awards and distinctions, including the MacArthur Fellowship "Genius Grant" in 2008. 
                Her works explore themes of identity, feminism, race, and the immigrant experience in contemporary society. 
                She divides her time between Nigeria, where she teaches writing workshops, and the United States. 
                Adichie\'s writing is celebrated for its insightful commentary on postcolonial African identity and the African diaspora.',
                'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Neil Gaiman',
                'bio' => 'Neil Richard MacKinnon Gaiman is an English author of short fiction, novels, comic books, graphic novels, audio theatre, and screenplays. 
His works include the comic book series The Sandman and novels Stardust, American Gods, Coraline, and The Graveyard Book. 
Gaiman\'s writing has won numerous awards, including the Hugo, Nebula, and Bram Stoker awards, as well as the Newbery and Carnegie medals. 
He is the first author to win both the Newbery and the Carnegie medals for the same work, The Graveyard Book. 
Gaiman\'s storytelling is characterized by dark fantasy, mythology, and fairy tale elements woven into contemporary settings. 
He frequently collaborates with other artists and has written scripts for television and film, including episodes of Doctor Who. 
Gaiman is also known for his active social media presence and engagement with fans. 
His works have been adapted into numerous films, television series, and audio productions, reaching a wide international audience.',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Isabel Allende',
                'bio' => 'Isabel Allende is a Chilean writer. Allende, whose works sometimes contain aspects of the "magic realist" tradition, is famous for novels such as The House of the Spirits and City of the Beasts. 
She has been called "the world\'s most widely read Spanish-language author." In 2004, Allende was inducted into the American Academy of Arts and Letters. 
She received Chile\'s National Literature Prize in 2010 and the Presidential Medal of Freedom from Barack Obama in 2014. 
Allende\'s novels are often based on her personal experience and historical events and pay homage to the lives of women, while weaving together elements of myth and realism. 
She has written over 20 books that have been translated into more than 35 languages and sold more than 70 million copies. 
Allende is known for her powerful storytelling that combines personal narrative with social and political commentary. 
She founded the Isabel Allende Foundation in 1996 to support programs that promote and preserve the fundamental rights of women and children.',
                'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=500&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Khaled Hosseini',
                'bio' => 'Khaled Hosseini is an Afghan-American novelist and physician. After graduating from college, he worked as a doctor in California, an occupation that he likened to "an arranged marriage." 
He has published three novels, most notably his 2003 debut The Kite Runner, all of which are at least partially set in Afghanistan and feature an Afghan as the protagonist. 
Following the success of The Kite Runner, Hosseini retired from medicine to write full-time. His second novel, A Thousand Splendid Suns, was released in 2007. 
His third novel, And the Mountains Echoed, was published on May 21, 2013. Hosseini was a Goodwill Ambassador for the UNHCR, the UN Refugee Agency. 
His writings focus on the human cost of conflict and displacement, exploring themes of family, redemption, and the immigrant experience. 
Hosseini\'s works have been adapted into films, stage productions, and graphic novels. 
He is also a founding member of The Khaled Hosseini Foundation, a nonprofit that provides humanitarian assistance to the people of Afghanistan.',
                'image' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert authors into database
        DB::table('authors')->insert($authors);

        $this->command->info('10 authors seeded successfully!');
    }
}
