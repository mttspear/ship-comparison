<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script src="https://unpkg.com/vue-select@3.0.0"></script>
    <link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Take Home Assignment</title>
    <style type="text/css" media="screen">
        table td{
            border:1px solid black;
        }
        .larger-diff{
            background-color: lightpink;
        }
    </style>

</head>
    <?php
        require 'vendor/autoload.php';
        require_once('api.php');
        require_once('ships.php');

        $api = New Api;
        $ships = New Ships;
        $shipsArray = $ships->getShips();
        $shipResults = $api->all();
    ?>
<body>
    <h2>Select two Starships from the dropdown lists to compare</h2>
    <div id="app">

        <div class="row">
            <div class="col-md-4">
                <v-select v-model="selectedOne" :options="ships"></v-select>
            </div>
            <div class="col-md-4">
                <v-select v-model="selectedTwo" :options="ships"></v-select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-default" v-on:click="setDisplayTable()">Compare</button>
            </div>
        </div>
        <div v-for="(result, index) in previousResults"  class="row">
            <div class="col-md-2">
                <button  v-if="index < 2" v-on:click="lookUp(result.oneName , result.twoName)" class="btn btn-default">{{ result.oneName }} vs {{ result.twoName }} </button>
            </div>
        </div>
        <div  v-if="displayTable"  class="row">
            <div class="col-md-6">
                <table style="width:100%">
                    <tr>
                        <th></th>
                        <th>Starship 1</th>
                        <th>Starship 2</th>
                    </tr>
                    <tr v-for="(field, index) in displayFields">
                        <td> {{ field.name }}</td>
                        <td v-bind:class="numDiff(field.propName, 'one')"> {{ shipOneDetails[field.propName] }}</td>
                        <td v-bind:class="numDiff(field.propName, 'two')"> {{ shipTwoDetails[field.propName] }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</body>

<script>
Vue.component('v-select', VueSelect.VueSelect);
app = new Vue({
    el: '#app',
    components: {

    },
    data: {
        previousResultsLog: [],
        displayTable: false,
        selectedOne: '',
        selectedTwo: '',
        shipOne: '',
        shipTwo: '',
        shipOneDetails: {},
        shipTwoDetails: {},
        displayFields: {
            name:{name:'Name',propName:'name'},
            cost:{name:'Cost',propName:'cost_in_credits'},
            speed:{name:'Speed',propName:'max_atmosphering_speed'},
            size:{name:'Cargo Size',propName:'max_atmosphering_speed'},
            passengers:{name:'Passengers',propName:'passengers'},
        },
        ships: <?php echo json_encode($shipsArray); ?>,
        shipDetails: <?php echo json_encode($shipResults); ?>
    },
    watch: {
        //look up the first selected value in the array
        selectedOne (val) {
            var result = this.shipDetails.find(obj => {
                return obj.name.toUpperCase() === val.toUpperCase()
            })
            this.shipOneDetails = result
        },
        //look up the second selected value in the array
        selectedTwo(val) {
            var result = this.shipDetails.find(obj => {
                return obj.name.toUpperCase() === val.toUpperCase()
            })
            this.shipTwoDetails = result;
        }
    },
    methods: {
        //If there is a numerical difference return a class
        numDiff: function(prop, ship) {
            shipOneValue = this.shipOneDetails[prop];
            shipTwoValue = this.shipTwoDetails[prop];
            if(this.isInt(shipOneValue) && this.isInt(shipTwoValue)){
                shipOneIntValue = parseInt(shipOneValue)
                shipTwoIntValue = parseInt(shipTwoValue)
                if(shipOneIntValue > shipTwoIntValue && ship == 'one'){
                    return 'larger-diff'
                }else if(shipOneIntValue < shipTwoIntValue && ship == 'two'){
                    return 'larger-diff'
                }
            }
        },

        //Test if varible is an int
        isInt(value){
            return !isNaN(value) && 
                parseInt(Number(value)) == value && 
                !isNaN(parseInt(value, 10));
        },

        //Use a function to set the lookup values
        lookUp(one,two){
            this.selectedOne = one
            this.selectedOne = two
        },

        //Turn on the table
        setDisplayTable(){
            this.displayTable = true;
        }
    },
    computed:{
        //Log the prevoius three results once both ships are set
        previousResults: function(){
            if(typeof this.shipOneDetails.name !== 'undefined' && typeof this.shipTwoDetails.name !== 'undefined'){
                lookUp = {oneName:this.shipOneDetails.name, twoName:this.shipTwoDetails.name}
                this.previousResultsLog.push(lookUp)
            }

            if(this.previousResultsLog.length > 3){
                this.previousResultsLog.shift()
            }

            return this.previousResultsLog; 
        }
    }

    })
</script>
</html>