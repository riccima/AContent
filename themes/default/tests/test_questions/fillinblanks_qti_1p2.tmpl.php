<!-- single answer multiple choice question -->
<item title="Multiple choice question" ident="ITEM_<?php echo $this->row['question_id']; ?>">
    <itemmetadata>
        <qtimetadata>
            <qtimetadatafield>
                <fieldlabel>qmd_itemtype</fieldlabel>
                <fieldentry>Logical Identifier</fieldentry>
            </qtimetadatafield>
            <qtimetadatafield>
                <fieldlabel>qmd_questiontype</fieldlabel>
                <fieldentry>Multiple-choice</fieldentry>
            </qtimetadatafield>
            <qtimetadatafield>
                <fieldlabel>cc_profile</fieldlabel>
                <fieldentry>cc.mutliple_choice.v0p1</fieldentry>
            </qtimetadatafield>
            <qtimetadatafield>
                <fieldlabel>cc_weighting</fieldlabel>
                <fieldentry><?php echo $this->weight; ?></fieldentry>
            </qtimetadatafield>
        </qtimetadata>
    </itemmetadata>
    <presentation>
        <flow>
            <?php
            $j = 0;
            $swap = 1;
            $park = explode(' ', $this->row['question']);


            for ($i = 0; $i < count($park); $i++) {

                if ($park[$this->row['answer_' . $j]] == $park[$i]) {
                    $j++;
                    if ($swap == 1) {
                        ?>               
                        <material>
                            <mattext texttype="text/html"> <?php echo $swap; ?> </mattext>
                        </material>
                        <?php
                    } else {
                        ?>
                        <material>
                            <mattext texttype="text/html"><?php echo $swap; ?></mattext>
                        </material>
                    <?php }
                    ?>
                    <response_str ident= "<?php echo $park[$i] . $i ?>" rcardinality="Single" rtiming="No">
                        <render_fib fibtype="String" prompt="Dashline" maxchars="6">
                        </render_fib>
                    </response_str>
        <?php
        $swap = "ciao";
    } else {

        $swap = $swap . " " . $park[$i];

        if ($i == (count($park) - 1)) {
            ?>
                        <material>
                            <mattext texttype="text/html"><?php echo $swap; ?></mattext>
                        </material>
            <?php
        }
    }
}
?>
        </flow>
    </presentation>
    <resprocessing>
        <outcomes>
            <decvar varname="SCORE" />
        </outcomes>
<?php for ($i = 0; $i < $this->num_choices; $i++): ?>
    <?php if ($this->row['answer_' . $i]): ?>
                <respcondition title="CorrectResponse">
                    <conditionvar>
                        <varequal respident="RESPONSE<?php echo $this->row['question_id']; ?>">Choice<?php echo $i; ?></varequal>
                    </conditionvar>
                    <setvar varname="que_score" action="Set"><?php echo (isset($this->row['weight'])) ? $this->row['weight'] : 1; ?></setvar>
                </respcondition>
    <?php endif; ?>
<?php endfor; ?>
    </resprocessing>
        <?php if ($this->row['feedback']): ?>
        <itemfeedback ident="FEEDBACK">
            <solution>
                <solutionmaterial>
                    <flow_mat>
                        <material>
                            <mattext texttype="text/html"><?php echo $this->row['feedback']; ?></mattext>
                        </material>
                    </flow_mat>
                </solutionmaterial>
            </solution>
        </itemfeedback>
<?php endif; ?>
</item>
