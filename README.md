# Renee Kellam's Personal Website
## About

A complete list of the files used to build my personal website (https://renee-kellam.com). Feel free to use them to make your own personal website! 
<br> <br>

## Unsure where to start?
Check here for a link to a pdf detailing how to make this website your own! 
(!!! WORK IN PROGRESS !!!)

## Notes:
### function <code>adminChecker()</code>
This function is an ***extremely*** simple checker function. If you need something more secure, I would recommend using authenticated user accounts and putting more information into the $_SESSION global variable that must all match.
<br>
To do this, create a <code>**users**</code> table that has information like user_id, username, etc. And include some of this information in the $_SESSION variable along with a randomly generated code on login. Then run a check that requires each peice of information to match with that on the table.


## Database Structure
### *jobs*
<table>
<tr>
    <th> Name </th>
    <th> Type </th>
    <th> Comments </th>
</tr>
<tr>
    <td> id </td>
    <td> int </td>
    <td class="comment"> Primary key</td>
</tr>
<tr>
    <td> employer </td>
    <td> text </td>
    <td class="comment"> Name of the employer </td>
</tr>
<tr>
    <td> title </td>
    <td> text </td>
    <td class="comment"> Job title</td>
</tr>
<tr>
    <td> start_date </td>
    <td> text </td>
    <td class="comment"> Date started in format month year (e.x. Feb 2024) </td>
</tr>
<tr>
    <td> end_date </td>
    <td> text </td>
    <td class="comment"> Date ended in format month year (e.x. Feb 2024) </td>
</tr>
<tr>
    <td> skills </td>
    <td> text </td>
    <td class="comment"> Skills used seperated by ","</td>
</tr>
<tr>
    <td> display_order </td>
    <td> int </td>
    <td class="comment"> Order jobs are displayed in, default is by title name </td>
</tr>
<tr>
    <td> hidden </td>
    <td> tinyint(1) </td>
    <td class="comment"> Hide from the front, Boolean </td>
</tr>
<tr>
    <td> date_added </td>
    <td> datetime </td>
    <td class="comment"> </td>
</tr>
<tr>
    <td> date_updated </td>
    <td> datetime </td>
    <td class="comment"> </td>
</tr>
</table>
<br>

#

### *library*

<table>
<tr>
    <th> Name </th>
    <th> Type </th>
    <th> Comments </th>
</tr>
<tr>
    <td> id </td>
    <td> int </td>
    <td class="comment"> Primary key</td>
</tr>
<tr>
    <td> author_first </td>
    <td> text </td>
    <td class="comment"> Primary / first listed author's first name </td>
</tr>
<tr>
    <td> author_last </td>
    <td> text </td>
    <td class="comment"> Primary / first listed author's last name, default sorted by </td>
</tr>
<tr>
    <td> authors </td>
    <td> text </td>
    <td class="comment"> All authors listed with "," seperation </td>
</tr>
<tr>
    <td> title </td>
    <td> text </td>
    <td class="comment"> Book title </td>
</tr>
<tr>
    <td> series </td>
    <td> text </td>
    <td class="comment"> Series title / name </td>
</tr>
<tr>
    <td> series_code </td>
    <td> int </td>
    <td class="comment"> Unique Code incase of series name double up (used to display series books)</td>
</tr>
<tr>
    <td> volume </td>
    <td> int </td>
    <td class="comment"> Volume number (Series Order) </td>
</tr>
<tr>
    <td> genre </td>
    <td> text </td>
    <td class="comment"> Book genre (e.x. Horror Comedy) </td>
</tr>
<tr>
    <td> type </td>
    <td> text </td>
    <td class="comment"> Type of book / literature (e.x. Scientific Paper, Novel, Graphic Novel, etc.) </td>
</tr>
<tr>
    <td> status </td>
    <td> text </td>
    <td class="comment"> Read, To-Read, Reading </td>
</tr>
<tr>
    <td> image </td>
    <td> text </td>
    <td class="comment"> File path to image of the cover </td>
</tr>
<tr>
    <td> rating </td>
    <td> int </td>
    <td class="comment"> Rating of the book on a scale of 1-5 </td>
</tr>
<tr>
    <td> hidden </td>
    <td> tinyint(1) </td>
    <td class="comment"> Hide from the front, Boolean </td>
</tr>
<tr>
    <td> date_added </td>
    <td> datetime </td>
    <td class="comment"> </td>
</tr>
<tr>
    <td> date_updated </td>
    <td> datetime </td>
    <td class="comment"> </td>
</tr>
</table>
<br>

#

### *projects*

<table>
<tr>
    <th> Name </th>
    <th> Type </th>
    <th> Comments </th>
</tr>
<tr>
    <td> id </td>
    <td> int </td>
    <td class="comment"> Project id </td>
</tr>
<tr>
    <td> name </td>
    <td> text </td>
    <td class="comment"> Project name </td>
</tr>
<tr>
    <td> description </td>
    <td> text </td>
    <td class="comment"> Description of the project </td>
</tr>
<tr>
    <td> super-description </td>
    <td> text </td>
    <td class="comment"> High level description of the project - shorter - for display </td>
</tr>
<tr>
    <td> link </td>
    <td> text </td>
    <td class="comment"> Link to project - if available </td>
</tr>
<tr>
    <td> link-text </td>
    <td> text </td>
    <td class="comment"> Displayed text for project link </td>
</tr>
<tr>
    <td> date-of-project </td>
    <td> year </td>
    <td class="comment"> Year the project was last worked on </td>
</tr>
<tr>
    <td> skills </td>
    <td> text </td>
    <td class="comment"> Skills used seperated by ","</td>
</tr>
<tr>
    <td> skills </td>
    <td> text </td>
    <td class="comment"> Skills used seperated by ","</td>
</tr>
<tr>
    <td> display_order </td>
    <td> int </td>
    <td class="comment"> Order jobs are displayed in, default is by title name </td>
</tr>
<tr>
    <td> hidden </td>
    <td> tinyint(1) </td>
    <td class="comment"> Hide from the front, Boolean </td>
</tr>
<tr>
    <td> date_added </td>
    <td> datetime </td>
    <td class="comment"> </td>
</tr>
<tr>
    <td> date_updated </td>
    <td> datetime </td>
    <td class="comment"> </td>
</tr>
</table>
<br>

#

### *skills*

<table>
<tr>
    <th> Name </th>
    <th> Type </th>
    <th> Comments </th>
</tr>
<tr>
    <td> id </td>
    <td> int </td>
    <td class="comment"> Skill id </td>
</tr>
<tr>
    <td> skill-name </td>
    <td> text </td>
    <td class="comment"> Name of the skill </td>
</tr>
</table>










<style>
    td {
        border: 1px black solid
    }
    table {
        border: collapse
    }
    .comment {
        font-style: italic
    }
</style>