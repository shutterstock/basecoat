<h2>To Do List Example</h2>
This project uses Basecoat in the context of a To Do list manager and presents usage of the database abstraction layer. Examples of bulk inserts are included through the use of auto-generated sample test data. Every page includes the output of the profiling feature of the database class to reveal all the queries that were run.

<h3>Setup</h3>
In the /config directory, run the todo.sql against the MySQL database of your naming. You must create the database first.
<code>
mysql -u [username] -p[password] [database] < todo.sql
</code>

Change database connection settings index.php file to reflect the setting for your database.